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

  # Note: the negative paths (a user without the capability gets a
  # required_capability_exception in import.php and panel.php) are covered in
  # PHPUnit (tests/external_test.php), because Behat treats the Moodle
  # exception page as a step failure and does not let us assert on it.

  # The manager archetype has CAP_ALLOW in db/access.php: it just gets in.
  Scenario: Un manager puede abrir la pantalla de importación
    Given I log in as "manager1"
    When I visit "/local/tresipuntimportgc/import.php"
    Then I should see "Import classes from Google Classroom"

  # With no OAuth client configured, connecting to Google is not offered: the
  # pending configuration warning is shown instead (the "noconfig" state).
  Scenario: Sin credenciales de Google el manager ve el aviso de configuración
    Given I log in as "manager1"
    When I visit "/local/tresipuntimportgc/import.php"
    Then I should see "The administrator has not configured the Google connection yet"
    And I should not see "Connect with Google"

  # With credentials saved but no Google session started, the screen moves to
  # the "connect" state: the three steps and the Google button (the header
  # title is static; the CTA lives in the body).
  Scenario: Con credenciales configuradas se ofrece conectar con Google
    Given the following config values are set as admin:
      | clientid  | test-client-id.apps.googleusercontent.com | local_tresipuntimportgc |
      | secretkey | test-secret                               | local_tresipuntimportgc |
    And I log in as "manager1"
    When I visit "/local/tresipuntimportgc/import.php"
    Then I should see "Connect with Google"
    And I should see "Connect your Google account and accept the read-only permissions."
    And I should not see "The administrator has not configured the Google connection yet"
