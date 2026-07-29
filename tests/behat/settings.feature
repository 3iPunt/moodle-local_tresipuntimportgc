@local @local_tresipuntimportgc
Feature: Página de ajustes del plugin
  Para configurar la conexión con Google,
  como administrador
  necesito ver los tres bloques de ajustes y la URI de redirección.

  # Comprueba que la página de ajustes carga con sus tres bloques (conexión,
  # opciones por defecto y registro) y que el bloque de conexión muestra la
  # URI de redirección y el estado "incompleta" cuando no hay credenciales.
  Scenario: El administrador ve los bloques de conexión, opciones y registro
    Given I log in as "admin"
    When I navigate to "Plugins > Tresipunt Import Google Classroom > Tresipunt Import Google Classroom settings" in site administration
    Then I should see "Google API connection"
    And I should see "Connection status"
    And I should see "Incomplete configuration"
    And I should see "Authorised redirect URI"
    And I should see "/local/tresipuntimportgc/import.php"
    And I should see "Default import options"
    And I should see "Import log"
    And I should see "Import history retention (days)"
    And the field "Import history retention (days)" matches value "365"
    And the field "Imports per page in the panel" matches value "25"

  # Con ID de cliente y secreto guardados, la pastilla de estado pasa a
  # "credenciales configuradas" y aparece el enlace de probar la conexión.
  Scenario: Con credenciales configuradas el bloque de conexión lo refleja
    Given the following config values are set as admin:
      | clientid  | test-client-id.apps.googleusercontent.com | local_tresipuntimportgc |
      | secretkey | test-secret                               | local_tresipuntimportgc |
    And I log in as "admin"
    When I navigate to "Plugins > Tresipunt Import Google Classroom > Tresipunt Import Google Classroom settings" in site administration
    Then I should see "Credentials configured"
    And I should see "Test connection"
