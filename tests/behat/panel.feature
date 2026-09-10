@local @local_tresipuntimportgc
Feature: Panel de importaciones
  Para supervisar las importaciones de Google Classroom,
  como manager
  necesito consultar el histórico con sus filtros.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | manager1 | Manager   | One      | manager1@example.com |
    And the following "system role assigns" exist:
      | user     | role    | contextlevel | reference |
      | manager1 | manager | System       |           |

  # Before the first import the panel shows the initial empty state, not a
  # table with no rows.
  Scenario: Estado vacío antes de cualquier importación
    Given I log in as "manager1"
    When I visit "/local/tresipuntimportgc/panel.php"
    Then I should see "No import has been run yet"

  # With data seeded by the generator (a run with one completed course and
  # another with an error), the history lists the import with who launched
  # it, the derived "with issues" status and the link to the detail.
  Scenario: El histórico lista las importaciones sembradas con estado y detalle
    Given the following "local_tresipuntimportgc > imports" exist:
      | user     | googleaccount       |
      | manager1 | teacher@example.com |
    And the following "local_tresipuntimportgc > import courses" exist:
      | user     | fullname   | shortname | status  |
      | manager1 | Biology 1  | bio1      | success |
      | manager1 | Physics 2  | phy2      | error   |
    And I log in as "manager1"
    When I visit "/local/tresipuntimportgc/panel.php"
    Then I should not see "No import has been run yet"
    And I should see "Manager One"
    And I should see "With issues"
    And I should see "View detail"
    # Privacy: a manager who is not a site administrator does not see the
    # Google account of whoever launched the import.
    And I should not see "teacher@example.com"

  # The status filter is applied server side through a GET parameter: if no
  # run matches, the "no results" state is shown (different from empty).
  Scenario: Filtrar por un estado sin coincidencias muestra el aviso de sin resultados
    Given the following "local_tresipuntimportgc > imports" exist:
      | user     |
      | manager1 |
    And the following "local_tresipuntimportgc > import courses" exist:
      | user     | fullname  | shortname | status  |
      | manager1 | Biology 1 | bio1      | success |
    And I log in as "manager1"
    When I visit "/local/tresipuntimportgc/panel.php?status=error"
    Then I should see "No import matches the filters"

  # The text search filters by who launched the import: with text that does
  # not match you see the warning; with the user name, the row.
  Scenario: La búsqueda por usuario filtra el histórico
    Given the following "local_tresipuntimportgc > imports" exist:
      | user     | googleaccount       |
      | manager1 | teacher@example.com |
    And the following "local_tresipuntimportgc > import courses" exist:
      | user     | fullname  | shortname | status  |
      | manager1 | Biology 1 | bio1      | success |
    And I log in as "manager1"
    When I visit "/local/tresipuntimportgc/panel.php?search=nadiellamadoasi"
    Then I should see "No import matches the filters"
    When I visit "/local/tresipuntimportgc/panel.php?search=Manager"
    Then I should see "Manager One"
    And I should see "View detail"
