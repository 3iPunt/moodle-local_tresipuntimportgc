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
 * Course selection screen: search, filters, selection and queue submit.
 *
 * @module     local_tresipuntimportgc/selection
 * @copyright  2026 3iPunt (contacte@tresipunt.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define([
    'core/str',
    'core/modal_save_cancel',
    'core/modal_events',
    'core/notification'
], function(Str, ModalSaveCancel, ModalEvents, Notification) {
    'use strict';

    var root = null;

    /**
     * All course row nodes.
     *
     * @returns {HTMLElement[]}
     */
    var rows = function() {
        return Array.prototype.slice.call(root.querySelectorAll('[data-course]'));
    };

    /**
     * Course rows currently visible (search + filter applied).
     *
     * @returns {HTMLElement[]}
     */
    var visibleRows = function() {
        return rows().filter(function(row) {
            return !row.hidden;
        });
    };

    /**
     * Course rows with the checkbox ticked.
     *
     * @returns {HTMLElement[]}
     */
    var selectedRows = function() {
        return rows().filter(function(row) {
            return row.querySelector('[data-action="toggle"]').checked;
        });
    };

    /**
     * Applies the search text and the state filter to the rows.
     */
    var applyFilters = function() {
        var text = root.querySelector('[data-action="search"]').value.trim().toLowerCase();
        var chip = root.querySelector('.tipgc-chip.active');
        var filter = chip ? chip.getAttribute('data-filter') : 'all';
        var shown = 0;
        rows().forEach(function(row) {
            var archived = row.getAttribute('data-archived') === '1';
            var name = (row.getAttribute('data-name') || '').toLowerCase();
            var visible = true;
            if (filter === 'active' && archived) {
                visible = false;
            }
            if (filter === 'archived' && !archived) {
                visible = false;
            }
            if (text !== '' && name.indexOf(text) === -1) {
                visible = false;
            }
            row.hidden = !visible;
            shown += visible ? 1 : 0;
        });
        var empty = root.querySelector('[data-region="noresults"]');
        if (empty) {
            empty.hidden = shown > 0;
        }
        refreshFooter();
    };

    /**
     * Updates the counter and the import button state.
     */
    var refreshFooter = function() {
        var count = selectedRows().length;
        var importbtn = root.querySelector('[data-action="import"]');
        importbtn.disabled = count === 0;
        Str.get_string('nselected', 'local_tresipuntimportgc', count).then(function(s) {
            root.querySelector('[data-region="selected-note"]').textContent = s;
            return s;
        }).catch(Notification.exception);
        var selectall = root.querySelector('[data-action="selectall"]');
        var visible = visibleRows();
        selectall.checked = visible.length > 0 && visible.every(function(row) {
            return row.querySelector('[data-action="toggle"]').checked;
        });
    };

    /**
     * Collects the configuration of every selected course.
     *
     * @returns {Object[]}
     */
    var collectConfigs = function() {
        return selectedRows().map(function(row) {
            var field = function(name) {
                return row.querySelector('[data-field="' + name + '"]');
            };
            var config = {
                providerid: row.getAttribute('data-id'),
                fullname: row.getAttribute('data-name'),
                shortname: field('shortname') ? field('shortname').value.trim() : '',
                categoryid: field('categoryid') ? field('categoryid').value : 0,
                visible: field('visible') ? field('visible').checked : true
            };
            if (field('importfiles')) {
                config.importfiles = field('importfiles').value;
            }
            if (field('calendarimport')) {
                config.calendarimport = field('calendarimport').value;
            }
            return config;
        });
    };

    /**
     * Opens the honest confirmation modal and submits the form on save.
     *
     * @param {Number} count Selected courses.
     */
    var confirmAndSubmit = function(count) {
        var strings = [
            {key: 'importmodal_title', component: 'local_tresipuntimportgc', param: count},
            {key: 'importmodal_body', component: 'local_tresipuntimportgc'},
            {key: 'importmodal_confirm', component: 'local_tresipuntimportgc'}
        ];
        Str.get_strings(strings).then(function(langstrings) {
            return ModalSaveCancel.create({
                title: langstrings[0],
                body: langstrings[1],
                buttons: {save: langstrings[2]},
                show: true
            }).then(function(modal) {
                modal.getRoot().on(ModalEvents.save, function() {
                    var form = root.querySelector('[data-region="import-form"]');
                    form.querySelector('[data-region="courses-payload"]').value =
                        JSON.stringify(collectConfigs());
                    form.submit();
                });
                return modal;
            });
        }).catch(Notification.exception);
    };

    /**
     * Wires all the listeners.
     */
    var register = function() {
        root.querySelector('[data-action="search"]').addEventListener('input', applyFilters);
        root.querySelectorAll('.tipgc-chip').forEach(function(chip) {
            chip.addEventListener('click', function() {
                root.querySelectorAll('.tipgc-chip').forEach(function(other) {
                    other.classList.remove('active');
                });
                chip.classList.add('active');
                applyFilters();
            });
        });
        root.querySelector('[data-action="selectall"]').addEventListener('change', function(e) {
            visibleRows().forEach(function(row) {
                row.querySelector('[data-action="toggle"]').checked = e.target.checked;
            });
            refreshFooter();
        });
        root.querySelector('[data-action="clear"]').addEventListener('click', function() {
            rows().forEach(function(row) {
                row.querySelector('[data-action="toggle"]').checked = false;
            });
            refreshFooter();
        });
        root.addEventListener('change', function(e) {
            if (e.target.matches('[data-action="toggle"]')) {
                refreshFooter();
            }
        });
        root.addEventListener('click', function(e) {
            var editbtn = e.target.closest('[data-action="edit"]');
            if (editbtn) {
                var row = editbtn.closest('[data-course]');
                var zone = row.querySelector('[data-region="edit"]');
                zone.hidden = !zone.hidden;
                editbtn.setAttribute('aria-expanded', zone.hidden ? 'false' : 'true');
            }
        });
        root.querySelector('[data-action="import"]').addEventListener('click', function() {
            var count = selectedRows().length;
            if (count > 0) {
                confirmAndSubmit(count);
            }
        });
    };

    return {
        /**
         * Entry point.
         *
         * @param {String} selector Root region selector.
         */
        init: function(selector) {
            root = document.querySelector(selector);
            if (!root || !root.querySelector('[data-action="search"]')) {
                // Connect/empty states have no listing to wire.
                return;
            }
            register();
            applyFilters();
        }
    };
});
