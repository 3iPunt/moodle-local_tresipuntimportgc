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
 * Ajax datasource for the course category autocomplete of the importer.
 *
 * Searches categories on the server so the selector scales with any number of
 * categories (no full list loaded into the page).
 *
 * @module     local_tresipuntimportgc/categories_datasource
 * @copyright  2026 3iPunt (contacte@tresipunt.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['core/ajax'], function(Ajax) {
    return {
        /**
         * Fetches matching categories from the web service.
         *
         * @param {String} selector The autocomplete selector.
         * @param {String} query The search text.
         * @param {Function} success Called with the raw results.
         * @param {Function} failure Called on error.
         */
        transport: function(selector, query, success, failure) {
            Ajax.call([{
                methodname: 'local_tresipuntimportgc_search_categories',
                args: {query: query}
            }])[0].then(success).catch(failure);
        },

        /**
         * Maps the web service results to autocomplete options.
         *
         * @param {String} selector The autocomplete selector.
         * @param {Array} results The raw category results.
         * @return {Array} Options as {value, label}.
         */
        processResults: function(selector, results) {
            return results.map(function(category) {
                return {value: category.id, label: category.name};
            });
        }
    };
});
