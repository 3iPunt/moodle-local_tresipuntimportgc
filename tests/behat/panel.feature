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

  # Antes de la primera importación el panel muestra el estado vacío
  # inicial, no una tabla sin filas.
  Scenario: Estado vacío antes de cualquier importación
    Given I log in as "manager1"
    When I visit "/local/tresipuntimportgc/panel.php"
    Then I should see "No import has been run yet"

  # Con datos sembrados por el generator (un run con un curso completado y
  # otro con error), el histórico lista la importación con quién la lanzó,
  # el estado derivado "con incidencias" y el enlace al detalle.
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

  # El filtro de estado se aplica en servidor vía parámetro GET: si ningún
  # run coincide, se muestra el estado "sin resultados" (distinto del vacío).
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

  # La búsqueda por texto filtra por quién lanzó la importación: con un
  # texto que no coincide se ve el aviso; con el nombre del usuario, la fila.
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
