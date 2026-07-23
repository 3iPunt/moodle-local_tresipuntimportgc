// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Import progress: incremental polling, live traces and course actions.
 *
 * @module     local_tresipuntimportgc/progress
 * @copyright  2026 3iPunt (contacte@tresipunt.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define([
    'core/ajax',
    'core/str',
    'core/modal_save_cancel',
    'core/modal_events',
    'core/notification'
], function(Ajax, Str, ModalSaveCancel, ModalEvents, Notification) {
    'use strict';

    var root = null;
    var importid = 0;
    var lastlogid = 0;
    var delay = 3000;
    var lastupdate = null;
    var strings = {};

    /** @type {Object} Badge label/class per course status (filled from lang strings). */
    var statusmeta = {};

    /**
     * Loads the strings used when painting live updates.
     *
     * @returns {Promise}
     */
    var loadStrings = function() {
        var requests = [
            {key: 'status_pending', component: 'local_tresipuntimportgc'},
            {key: 'status_running', component: 'local_tresipuntimportgc'},
            {key: 'status_success', component: 'local_tresipuntimportgc'},
            {key: 'status_error', component: 'local_tresipuntimportgc'},
            {key: 'status_discarded', component: 'local_tresipuntimportgc'},
            {key: 'updatednote', component: 'local_tresipuntimportgc'},
            {key: 'retrymodal_title', component: 'local_tresipuntimportgc'},
            {key: 'retrymodal_body', component: 'local_tresipuntimportgc'},
            {key: 'retry', component: 'local_tresipuntimportgc'},
            {key: 'discardmodal_title', component: 'local_tresipuntimportgc'},
            {key: 'discardmodal_body', component: 'local_tresipuntimportgc'},
            {key: 'discard', component: 'local_tresipuntimportgc'}
        ];
        return Str.get_strings(requests).then(function(results) {
            statusmeta = {
                pending: {label: results[0], cls: 'tipgc-badge-pending'},
                running: {label: results[1], cls: 'tipgc-badge-running'},
                success: {label: results[2], cls: 'tipgc-badge-success'},
                error: {label: results[3], cls: 'tipgc-badge-error'},
                discarded: {label: results[4], cls: 'tipgc-badge-pending'}
            };
            strings.updatednote = results[5];
            strings.retrytitle = results[6];
            strings.retrybody = results[7];
            strings.retrybtn = results[8];
            strings.discardtitle = results[9];
            strings.discardbody = results[10];
            strings.discardbtn = results[11];
            return results;
        });
    };

    /**
     * Formats a unix timestamp as HH:MM:SS.
     *
     * @param {Number} timestamp Unix seconds.
     * @returns {String}
     */
    var formatTime = function(timestamp) {
        var date = new Date(timestamp * 1000);
        var pad = function(n) {
            return String(n).padStart(2, '0');
        };
        return pad(date.getHours()) + ':' + pad(date.getMinutes()) + ':' + pad(date.getSeconds());
    };

    /**
     * Applies one course payload to its card (badge, link, actions, traces).
     *
     * @param {Object} course WS course payload.
     * @returns {Boolean} Whether anything changed.
     */
    var applyCourse = function(course) {
        var card = root.querySelector('[data-importcourse="' + course.id + '"]');
        if (!card) {
            return false;
        }
        var changed = false;
        if (card.getAttribute('data-status') !== course.status) {
            card.setAttribute('data-status', course.status);
            var meta = statusmeta[course.status];
            var badge = card.querySelector('[data-region="badge"]');
            badge.textContent = meta.label;
            badge.className = 'tipgc-badge ' + meta.cls + ' ml-2 ms-2';
            card.querySelector('[data-action="retry"]').classList.toggle('d-none', course.status !== 'error');
            card.querySelector('[data-action="discard"]').classList.toggle('d-none', course.status !== 'pending');
            changed = true;
        }
        if (course.courseurl) {
            var link = card.querySelector('[data-region="course-link"]');
            if (link.classList.contains('d-none')) {
                link.href = course.courseurl;
                link.classList.remove('d-none');
                changed = true;
            }
        }
        if (course.logs.length > 0) {
            var wrap = card.querySelector('[data-region="logs-wrap"]');
            var lines = card.querySelector('[data-region="logs"]');
            var atbottom = lines.scrollHeight - lines.scrollTop - lines.clientHeight < 30;
            var errors = 0;
            course.logs.forEach(function(log) {
                var line = document.createElement('div');
                line.className = 'tipgc-log-line tipgc-log-' + log.level;
                line.setAttribute('data-level', log.level);
                var time = document.createElement('span');
                time.className = 'tipgc-mono tipgc-log-time';
                time.textContent = formatTime(log.timecreated);
                var msg = document.createElement('span');
                msg.className = 'tipgc-log-msg';
                msg.innerHTML = log.message;
                line.appendChild(time);
                line.appendChild(msg);
                applyLogFilter(card, line);
                lines.appendChild(line);
                errors += log.level === 'error' ? 1 : 0;
            });
            wrap.classList.remove('d-none');
            if (atbottom) {
                lines.scrollTop = lines.scrollHeight;
            }
            if (errors > 0) {
                var counter = card.querySelector('[data-region="error-count"]');
                var n = card.querySelector('[data-region="error-count-n"]');
                n.textContent = String(parseInt(n.textContent || '0', 10) + errors);
                counter.classList.remove('d-none');
            }
            changed = true;
        }
        return changed;
    };

    /**
     * Recomputes the global bar and the counters from the card statuses.
     */
    var refreshBar = function() {
        var counts = {pending: 0, running: 0, success: 0, error: 0, discarded: 0};
        root.querySelectorAll('[data-importcourse]').forEach(function(card) {
            counts[card.getAttribute('data-status')]++;
        });
        var total = counts.pending + counts.running + counts.success + counts.error;
        var pct = function(n) {
            return total > 0 ? (n / total * 100) : 0;
        };
        root.querySelector('[data-region="seg-success"]').style.width = pct(counts.success) + '%';
        root.querySelector('[data-region="seg-error"]').style.width = pct(counts.error) + '%';
        root.querySelector('[data-region="seg-running"]').style.width = pct(counts.running) + '%';
        root.querySelector('[data-region="count-success"]').textContent = counts.success;
        root.querySelector('[data-region="count-error"]').textContent = counts.error;
        root.querySelector('[data-region="count-running"]').textContent = counts.running;
        root.querySelector('[data-region="count-pending"]').textContent = counts.pending;
    };

    /**
     * Updates the "updated X s ago" note.
     */
    var tickNote = function() {
        var note = root.querySelector('[data-region="refresh-note"]');
        if (!note || lastupdate === null) {
            return;
        }
        var seconds = Math.max(0, Math.round((Date.now() - lastupdate) / 1000));
        note.textContent = strings.updatednote.replace('{$a}', seconds);
    };

    /**
     * One polling round; reschedules itself until the run finishes.
     */
    var poll = function() {
        Ajax.call([{
            methodname: 'local_tresipuntimportgc_import_get_status',
            args: {importid: importid, lastlogid: lastlogid}
        }])[0].then(function(response) {
            lastlogid = Math.max(lastlogid, response.maxlogid);
            lastupdate = Date.now();
            var changed = false;
            response.courses.forEach(function(course) {
                changed = applyCourse(course) || changed;
            });
            refreshBar();
            if (response.finished) {
                // Reload once: the server renders the final summary.
                window.location.reload();
                return response;
            }
            delay = changed ? 3000 : Math.min(delay + 2000, 10000);
            setTimeout(poll, delay);
            return response;
        }).catch(function() {
            // Transient error (network, session): retry more slowly.
            delay = Math.min(delay + 5000, 20000);
            setTimeout(poll, delay);
        });
    };

    /**
     * Applies the active trace filter of a card to one line.
     *
     * @param {HTMLElement} card Course card.
     * @param {HTMLElement} line Trace line.
     */
    var applyLogFilter = function(card, line) {
        var chip = card.querySelector('.tipgc-chip.active');
        var filter = chip ? chip.getAttribute('data-logfilter') : 'all';
        line.hidden = filter !== 'all' && line.getAttribute('data-level') !== filter;
    };

    /**
     * Confirms and executes a retry/discard action.
     *
     * @param {String} action retry|discard.
     * @param {Number} id Import course id.
     * @param {String} name Course name.
     */
    var confirmAction = function(action, id, name) {
        var title = action === 'retry' ? strings.retrytitle : strings.discardtitle;
        var body = (action === 'retry' ? strings.retrybody : strings.discardbody).replace('{$a}', name);
        var button = action === 'retry' ? strings.retrybtn : strings.discardbtn;
        ModalSaveCancel.create({
            title: title,
            body: body,
            buttons: {save: button},
            show: true
        }).then(function(modal) {
            modal.getRoot().on(ModalEvents.save, function() {
                Ajax.call([{
                    methodname: 'local_tresipuntimportgc_import_' + action + '_course',
                    args: {importcourseid: id}
                }])[0].then(function(response) {
                    if (response.success) {
                        window.location.reload();
                    } else {
                        Notification.addNotification({message: response.message, type: 'error'});
                    }
                    return response;
                }).catch(Notification.exception);
            });
            return modal;
        }).catch(Notification.exception);
    };

    /**
     * Wires filters and action buttons.
     */
    var register = function() {
        root.addEventListener('click', function(e) {
            var chip = e.target.closest('[data-logfilter]');
            if (chip) {
                var card = chip.closest('[data-importcourse]');
                card.querySelectorAll('[data-logfilter]').forEach(function(other) {
                    other.classList.remove('active');
                });
                chip.classList.add('active');
                card.querySelectorAll('.tipgc-log-line').forEach(function(line) {
                    applyLogFilter(card, line);
                });
                return;
            }
            var button = e.target.closest('[data-action="retry"], [data-action="discard"]');
            if (button) {
                confirmAction(button.getAttribute('data-action'),
                    parseInt(button.getAttribute('data-id'), 10),
                    button.getAttribute('data-name'));
            }
        });
    };

    return {
        /**
         * Entry point.
         *
         * @param {String} selector Root region selector.
         * @param {Boolean} live Whether the run is still open (enables polling).
         */
        init: function(selector, live) {
            root = document.querySelector(selector);
            if (!root) {
                return;
            }
            importid = parseInt(root.getAttribute('data-importid'), 10);
            lastlogid = parseInt(root.getAttribute('data-lastlogid'), 10);
            loadStrings().then(function() {
                register();
                if (live) {
                    lastupdate = Date.now();
                    setInterval(tickNote, 1000);
                    setTimeout(poll, delay);
                }
                return true;
            }).catch(Notification.exception);
        }
    };
});