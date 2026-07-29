@local @local_tresipuntimportgc
Feature: Pantalla de progreso en modo histórico
  Para revisar una importación terminada,
  como manager
  necesito ver los estados por curso, las trazas y las acciones.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | manager1 | Manager   | One      | manager1@example.com |
    And the following "system role assigns" exist:
      | user     | role    | contextlevel | reference |
      | manager1 | manager | System       |           |
    And the following "local_tresipuntimportgc > imports" exist:
      | user     | googleaccount       |
      | manager1 | teacher@example.com |
    And the following "local_tresipuntimportgc > import courses" exist:
      | user     | fullname  | shortname | status  |
      | manager1 | Biology 1 | bio1      | success |
      | manager1 | Physics 2 | phy2      | error   |
    And the following "local_tresipuntimportgc > logs" exist:
      | shortname | level | message                        |
      | bio1      | info  | Course created without issues  |
      | phy2      | error | The short name is already used |

  # Importación sembrada con un curso completado y otro con error: al abrir
  # el detalle desde el panel se ve la cabecera estática, el estado derivado
  # "con incidencias", ambos cursos, sus trazas, el resumen final y el botón
  # de reintentar en el curso fallido (sin @javascript: solo presencia).
  Scenario: Una importación terminada muestra resumen, pastillas, trazas y la acción de reintentar
    Given I log in as "manager1"
    When I visit "/local/tresipuntimportgc/panel.php"
    And I follow "View detail"
    Then I should see "Import progress"
    And I should see "With issues"
    And I should see "Biology 1"
    And I should see "Physics 2"
    And I should see "Course created without issues"
    And I should see "The short name is already used"
    And I should see "Import summary"
    And "button[data-action='retry']" "css_element" should exist
    And I should see "teacher@example.com"

  # Con un curso aún pendiente la importación no está terminada: no hay
  # resumen final y el curso pendiente ofrece la acción de descartar.
  Scenario: Una importación con cursos pendientes ofrece descartar y no muestra resumen
    Given the following "local_tresipuntimportgc > import courses" exist:
      | user     | fullname    | shortname | status  |
      | manager1 | Chemistry 3 | che3      | pending |
    And I log in as "manager1"
    When I visit "/local/tresipuntimportgc/panel.php"
    And I follow "View detail"
    Then I should see "Chemistry 3"
    And "button[data-action='discard']" "css_element" should exist
    And I should not see "Import summary"
