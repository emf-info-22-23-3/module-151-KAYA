<?php

require_once 'workers/UserManager.php';
require_once 'workers/ArticleManager.php';
require_once 'helpers/DBArticleManager.php';
require_once 'helpers/DBUserManager.php';
require_once 'helpers/DBConfig.php';
require_once 'helpers/DBConnection.php';
require_once 'beans/Set.php';
require_once 'beans/Source.php';
require_once 'beans/User.php';

session_start();

/**
 * @author Kaya
 */

// Helper function for consistent XML responses
function sendXMLResponse($success, $message = '', $data = null) {
    header('Content-Type: text/xml');
    echo "<?xml version='1.0' encoding='UTF-8'?>\n";
    echo "<response>\n";
    echo "  <success>" . ($success ? 'true' : 'false') . "</success>\n";
    if ($message) {
        echo "  <message>" . htmlspecialchars($message) . "</message>\n";
    }
    if ($data) {
        foreach ($data as $key => $value) {
            echo "  <" . htmlspecialchars($key) . ">" . htmlspecialchars($value) . "</" . htmlspecialchars($key) . ">\n";
        }
    }
    echo "</response>";
    exit;
}

// Consistent session handling functions
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'Administrator';
}

function login($user) {
    $_SESSION['user_id'] = $user->getPK();
    $_SESSION['email'] = $user->getEmail();
    $_SESSION['role'] = $user->getRole();
}

function logout() {
    session_unset();
    session_destroy();
}

// Initialize managers
$userManager = new UserManager();
$articleManager = new ArticleManager();

// Handle different HTTP methods
switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
        $action = $_POST['action'] ?? '';
        
        switch($action) {
            case 'login':
                $email = $_POST['email'] ?? '';
                $password = $_POST['password'] ?? '';
                
                $user = $userManager->checkCredentials($email, $password);
                if ($user) {
                    login($user);
                    sendXMLResponse(true, 'Login successful', [
                        'isAdmin' => $user->getRole() === 'Administrator' ? 'true' : 'false',
                        'email' => $user->getEmail(),
                        'role' => $user->getRole()
                    ]);
                } else {
                    sendXMLResponse(false, 'Invalid credentials');
                }
                break;

            case 'disconnect':
                logout();
                sendXMLResponse(true, 'Logout successful');
                break;

            case 'addSet':
                if (!isLoggedIn()) {
                    sendXMLResponse(false, 'Please log in first');
                    break;
                }

                if (!isAdmin()) {
                    sendXMLResponse(false, 'Administrator access required');
                    break;
                }

                $set = new Set(
                    null,
                    $_SESSION['user_id'],
                    $_POST['nom'] ?? '',
                    $_POST['cap_nom'] ?? '',
                    $_POST['tunic_nom'] ?? '',
                    $_POST['trousers_nom'] ?? '',
                    $_POST['description'] ?? '',
                    $_POST['effet'] ?? '',
                    $_POST['fk_cap_source'] ?? '',
                    $_POST['fk_tunic_source'] ?? '',
                    $_POST['fk_trousers_source'] ?? ''
                );

                $result = $articleManager->addSet($set);
                sendXMLResponse($result !== false, $result ? 'Set added successfully' : 'Failed to add set');
                break;

            default:
                sendXMLResponse(false, 'Invalid action');
                break;
        }
        break;

    case 'GET':
        if (!isLoggedIn()) {
            sendXMLResponse(false, 'Please log in first');
            break;
        }

        $sets = $articleManager->getAnnonces();
        header('Content-Type: text/xml');
        echo $articleManager->convertSetsToXML($sets);
        break;

    case 'PUT':
        if (!isLoggedIn()) {
            sendXMLResponse(false, 'Please log in first');
            break;
        }

        if (!isAdmin()) {
            sendXMLResponse(false, 'Administrator access required');
            break;
        }

        parse_str(file_get_contents("php://input"), $putData);
        $set = new Set(
            $putData['pk_set'] ?? null,
            $_SESSION['user_id'],
            $putData['nom'] ?? '',
            $putData['cap_nom'] ?? '',
            $putData['tunic_nom'] ?? '',
            $putData['trousers_nom'] ?? '',
            $putData['description'] ?? '',
            $putData['effet'] ?? '',
            $putData['fk_cap_source'] ?? '',
            $putData['fk_tunic_source'] ?? '',
            $putData['fk_trousers_source'] ?? ''
        );

        $result = $articleManager->updateSet($set);
        sendXMLResponse($result, $result ? 'Set updated successfully' : 'Failed to update set');
        break;

    case 'DELETE':
        if (!isLoggedIn()) {
            sendXMLResponse(false, 'Please log in first');
            break;
        }

        if (!isAdmin()) {
            sendXMLResponse(false, 'Administrator access required');
            break;
        }

        $setId = $_GET['id'] ?? null;
        if (!$setId) {
            sendXMLResponse(false, 'Set ID is required');
            break;
        }

        $result = $articleManager->deleteSet($setId);
        sendXMLResponse($result, $result ? 'Set deleted successfully' : 'Failed to delete set');
        break;

    default:
        sendXMLResponse(false, 'Invalid request method');
        break;
}
?>