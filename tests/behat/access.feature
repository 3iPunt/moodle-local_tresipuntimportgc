@local @local_tresipuntimportgc
Feature: Control de acceso a las pantallas de importación
  Para que solo los usuarios autorizados puedan crear cursos,
  como sitio
  necesito que las pantallas del plugin exijan sus capacidades.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | One      | student1@example.com |
      | manager1 | Manager   | One      | manager1@example.com |
    And the following "system role assigns" exist:
      | user     | role    | contextlevel | reference |
      | manager1 | manager | System       |           |

  # Nota: los caminos negativos (usuario sin capacidad recibe
  # required_capability_exception en import.php y panel.php) se cubren en
  # PHPUnit (tests/external_test.php), porque Behat trata la página de
  # excepción de Moodle como fallo del paso y no permite asertarla.

  # El arquetipo manager tiene CAP_ALLOW en db/access.php: entra sin más.
  Scenario: Un manager puede abrir la pantalla de importación
    Given I log in as "manager1"
    When I visit "/local/tresipuntimportgc/import.php"
    Then I should see "Import classes from Google Classroom"

  # Sin cliente OAuth configurado no se ofrece conectar con Google: se
  # muestra el aviso de configuración pendiente (estado "noconfig").
  Scenario: Sin credenciales de Google el manager ve el aviso de configuración
    Given I log in as "manager1"
    When I visit "/local/tresipuntimportgc/import.php"
    Then I should see "The administrator has not configured the Google connection yet"
    And I should not see "Connect with Google"

  # Con credenciales guardadas pero sin sesión de Google iniciada, la
  # pantalla pasa al estado "conectar": los tres pasos y el botón de Google
  # (el título de la cabecera es estático; el CTA vive en el cuerpo).
  Scenario: Con credenciales configuradas se ofrece conectar con Google
    Given the following config values are set as admin:
      | clientid  | test-client-id.apps.googleusercontent.com | local_tresipuntimportgc |
      | secretkey | test-secret                               | local_tresipuntimportgc |
    And I log in as "manager1"
    When I visit "/local/tresipuntimportgc/import.php"
    Then I should see "Connect with Google"
    And I should see "Connect your Google account and accept the read-only permissions."
    And I should not see "The administrator has not configured the Google connection yet"
