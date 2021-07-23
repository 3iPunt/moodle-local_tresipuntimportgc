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
 * @package local_tresipuntimportgc
 * @author 3iPunt <https://www.tresipunt.com/>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright 3iPunt <https://www.tresipunt.com/>
 */

/* eslint-disable no-unused-vars */
/* eslint-disable no-console */

define([
    'jquery',
    'core/str',
    'core/ajax',
    'core/modal_factory',
    'core/modal_events',
    'core/templates'
], function($, Str, Ajax, ModalFactory, ModalEvents, Templates) {
    "use strict";

    /**
     * List of action selectors used by this module.
     *
     * @property {string} CREATECOURSES
     */
    let ACTION = {
        CREATECOURSES: '[data-action="createcourses"]'
    };

    /**
     * List of services used by this module.
     *
     * @property {string} CREATECOURSES
     */
    let SERVICES = {
        CREATECOURSES: 'local_tresipuntimportgc_createcourses'
    };

    /**
     * List of regions used by reloads.
     *
     * @property {string} CONTENT_LIST - Region for all reload
     * @property {string} CHECKS
     * @property {string} ALLCHEKS
     */
    let REGION = {
        CONTENT_LIST: '[data-region="content-courses"]',
        CHECKS: 'input[type="checkbox"]:not(#allchecks)',
        ALLCHEKS: '#allchecks'
    };

    /**
     * List of templates used in reloads.
     *
     * @property {string} PAGE
     */
    let TEMPLATES = {
        PAGE: 'local_tresipuntimportgc/import_page'
    };

    /**
     * @constructor
     * @param {String} selector
     */
    function Controller(selector) {
        this.node = $(selector);
        this.initControl();
        this.initCreatecourses();
    }

    /** @type {jQuery} The jQuery node for the page region. */
    Controller.prototype.node = null;

    Controller.prototype.initControl = function() {
        this.checkButton.bind(this);
        this.node.find(REGION.CHECKS).on('click', this.checkButton.bind(this));
        this.node.find(REGION.ALLCHEKS).on('click', this.allChecks.bind(this));
    };

    Controller.prototype.allChecks = function(e) {
        if (e.target.checked === true) {
            this.node.find(REGION.CHECKS).each(function() {
                $(this).prop('checked', true);
                $(ACTION.CREATECOURSES).removeAttr('disabled');
            });
        } else {
            this.node.find(REGION.CHECKS).each(function() {
                $(this).prop('checked', false);
                $(ACTION.CREATECOURSES).prop('disabled', true);
            });
        }
        this.checkButton.bind(this);
    };

    Controller.prototype.checkButton = function() {
        let somechecked = false;
        this.node.find(REGION.CHECKS).each(function() {
            if ($(this).is(':checked')) {
                somechecked = true;
                return false;
            }
        });
        if (somechecked) {
            $(ACTION.CREATECOURSES).removeAttr('disabled');
        } else {
            $(ACTION.CREATECOURSES).prop('disabled', true);
        }
    };

    /**
     * Register event listeners of click.
     */
    Controller.prototype.initCreatecourses = function() {
        this.node.find(ACTION.CREATECOURSES).on('click', this.initCreation.bind(this));
    };

    Controller.prototype.initCreation = function(e) {
        e.preventDefault();
        e.stopPropagation();
        let identifier = $(REGION.CONTENT_LIST);
        let stringkeys = [
            {key: 'createcourses', component: 'local_tresipuntimportgc'},
            {key: 'createcourses_help', component: 'local_tresipuntimportgc'},
            {key: 'create', component: 'local_tresipuntimportgc'},
        ];
        Str.get_strings(stringkeys).then(function(langStrings) {
            let title = langStrings[0];
            let confirmMessage = langStrings[1];
            let buttonText = langStrings[2];
            return ModalFactory.create({
                title: title,
                body: confirmMessage,
                type: ModalFactory.types.SAVE_CANCEL
            }).then(function(modal) {
                modal.setSaveButtonText(buttonText);
                modal.getRoot().on(ModalEvents.save, function() {
                    let courses = [];
                    identifier.find(REGION.CHECKS).each(function() {
                        if ($(this).is(':checked')) {
                            courses.push($(this).attr('data-id'));
                        }
                    });
                    let param = courses.join('__');
                    let url = window.location.href + '?courses=' + btoa(param);
                    let tasktab = window.open(url, '_self');
                    tasktab.focus();
                });
                modal.getRoot().on(ModalEvents.hidden, function() {
                    modal.destroy();
                });
                return modal;
            });
        }).done(function(modal) {
            modal.show();
        }).fail(Notification.exception);
    };

    return {
        /**
         * @param {String} selector The selector for the page region containing the page.
         * @return {Controller}
         */
        init: function(selector) {
            return new Controller(selector);
        }
    };
});
