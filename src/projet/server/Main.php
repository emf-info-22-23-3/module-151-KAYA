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
require_once 'beans/SourceType.php';

session_start();

/**
 * @author Kaya
 */

/**
 * Helper function for consistent XML responses that can handle array data
 * 
 * @param bool $success Whether the operation was successful
 * @param string $message Optional message to include in response
 * @param array $data Optional data to include in response
 */
function sendXMLResponse($success, $message = '', $data = null) {

    echo "<response>\n";
    echo "  <success>" . ($success ? 'true' : 'false') . "</success>\n";
    
    if ($message) {
        echo "  <message>" . htmlspecialchars($message, ENT_XML1, 'UTF-8') . "</message>\n";
    }
    
    if ($data) {
        if (isset($data['set'])) {
            // Handle the case where 'set' is an associative array
            $set = $data['set'];
            echo "  <setWanted>\n";
            foreach ($set as $key => $value) {
                echo "    <" . htmlspecialchars($key, ENT_XML1, 'UTF-8') . ">" 
                     . htmlspecialchars($value, ENT_XML1, 'UTF-8') 
                     . "</" . htmlspecialchars($key, ENT_XML1, 'UTF-8') . ">\n";
            }
            echo "  </setWanted>\n";
        } 
        
        if (isset($data['sourceTypes']) && is_array($data['sourceTypes']) && count($data['sourceTypes']) > 0) {
            echo "  <sourceTypes>\n";
            
            // Loop through the sourceTypes array
            foreach ($data as $key => $items) {
                // Start the container element (e.g., <sourceTypes>)
                echo "  <" . htmlspecialchars($key, ENT_XML1, 'UTF-8') . ">\n";
                if (is_array($items)) {
                    foreach ($items as $item) {
                        if (is_array($item)) {
                            echo "    <sourceType>\n";
                            foreach ($item as $itemKey => $itemValue) {
                               echo "      <" . htmlspecialchars($itemKey, ENT_XML1, 'UTF-8') . ">" 
                                    . htmlspecialchars($itemValue, ENT_XML1, 'UTF-8') 
                                    . "</" . htmlspecialchars($itemKey, ENT_XML1, 'UTF-8') . ">\n";
                            }
                            echo "    </sourceType>\n";
                        }
                    }
                }
                // Close the container element
                echo "  </" . htmlspecialchars($key, ENT_XML1, 'UTF-8') . ">\n";
            }
            
            echo "  </sourceTypes>\n";

        } else {
            foreach ($data as $key => $items) {
                // Start the container element (e.g., <armorNames>)
                echo "  <" . htmlspecialchars($key, ENT_XML1, 'UTF-8') . ">\n";
                if (is_array($items)) {
                    foreach ($items as $item) {
                        if (is_array($item)) {
                            echo "    <armor>\n";
                            foreach ($item as $itemKey => $itemValue) {
                               echo "      <" . htmlspecialchars($itemKey, ENT_XML1, 'UTF-8') . ">" 
                                    . htmlspecialchars($itemValue, ENT_XML1, 'UTF-8') 
                                    . "</" . htmlspecialchars($itemKey, ENT_XML1, 'UTF-8') . ">\n";
                            }
                            echo "    </armor>\n";
                        }
                    }
                }
                // Close the container element
                echo "  </" . htmlspecialchars($key, ENT_XML1, 'UTF-8') . ">\n";
            }
        }

        echo "</response>";
        exit;
    }
}

function sendXMLResponseLogin($success, $message = '', $data = null) {
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
        echo "Action: $action\n";  // Debugging the received action (add this for debugging)
        
        switch($action) {
            case 'login':
                $email = $_POST['email'] ?? '';
                $password = $_POST['password'] ?? '';
                
                $user = $userManager->checkCredentials($email, $password);
                if ($user) {
                    login($user);
                    sendXMLResponseLogin(true, 'Login successful', [
                        'isAdmin' => $user->getRole() === 'Administrator' ? 'true' : 'false',
                        'email' => $user->getEmail(),
                        'role' => $user->getRole()
                    ]);
                } else {
                    sendXMLResponseLogin(false, 'Invalid credentials');
                }
                break;

            case 'disconnect':
                logout();
                sendXMLResponseLogout(true, 'Logout successful');
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
                    // Collecting form data
    $armorName = $_POST['armorName'] ?? '';
    echo "Armor Name: $armorName\n";  // Debugging armor name
    $armorCapName = $_POST['armorCapName'] ?? '';
    echo "Cap Name: $armorCapName\n";  // Debugging cap name
    $armorTunicName = $_POST['armorTunicName'] ?? '';
    echo "Tunic Name: $armorTunicName\n";  // Debugging tunic name
    $armorTrousersName = $_POST['armorTrousersName'] ?? '';
    echo "Trousers Name: $armorTrousersName\n";  // Debugging trousers name
    $armorEffect = $_POST['armorEffect'] ?? '';
    echo "Effect: $armorEffect\n";  // Debugging effect
    $armorDescription = $_POST['armorDescription'] ?? '';
    echo "Description: $armorDescription\n";  // Debugging description

    // Collecting source type and source values
    $armorCapSourceType = $_POST['armorCapSourceType'] ?? '';
    echo "Cap Source Type: $armorCapSourceType\n";  // Debugging cap source type
    $armorCapSource = $_POST['armorCapSource'] ?? '';
    echo "Cap Source: $armorCapSource\n";  // Debugging cap source
    $armorTunicSourceType = $_POST['armorTunicSourceType'] ?? '';
    echo "Tunic Source Type: $armorTunicSourceType\n";  // Debugging tunic source type
    $armorTunicSource = $_POST['armorTunicSource'] ?? '';
    echo "Tunic Source: $armorTunicSource\n";  // Debugging tunic source
    $armorTrousersSourceType = $_POST['armorTrousersSourceType'] ?? '';
    echo "Trousers Source Type: $armorTrousersSourceType\n";  // Debugging trousers source type
    $armorTrousersSource = $_POST['armorTrousersSource'] ?? '';
    echo "Trousers Source: $armorTrousersSource\n";  // Debugging trousers source
                
                    // Handling the uploaded image (if exists)
                    if (isset($_FILES['armorImage']) && $_FILES['armorImage']['error'] === UPLOAD_ERR_OK) {
                        $imageTempPath = $_FILES['armorImage']['tmp_name'];
                        $imageName = $_FILES['armorImage']['name'];
                        $imagePath = 'uploads/' . $imageName;
                        move_uploaded_file($imageTempPath, $imagePath);
                    } else {
                        $imagePath = ''; // Or handle the case where no image was uploaded
                    }

                    $armorCapSourceNew = new Source(
                        null,
                        $armorCapSource,
                        $armorCapSourceType
                    );

                    $armorTunicSourceNew = new Source(
                        null,
                        $armorTunicSource,
                        $armorTunicSourceType
                    );

                    $armorTrousersSourceNew = new Source(
                        null,
                        $armorTrousersSource,
                        $armorTrousersSourceType
                    );

                    // Create threee Source object with the sourceType data and source data
                    $armorCapSourceId = $articleManager->addSource($armorCapSourceNew);
                    $armorTunicSourceId = $articleManager->addSource($armorTunicSourceNew);
                    $armorTrousersSourceId = $articleManager->addSource($armorTrousersSourceNew);
                
                    // Create a Set object with the collected data
                    $set = new Set(
                        null,
                        $_SESSION['user_id'],
                        $armorName,
                        $armorCapName,
                        $armorTunicName,
                        $armorTrousersName,
                        $armorDescription,
                        $armorEffect,
                        $armorCapSourceId,
                        $armorTunicSourceId,
                        $armorTrousersSourceId,
                        $imagePath
                    );
                
                    // Call the manager to add the set
                    $result = $articleManager->addSet($set);
                
                    sendXMLResponse($result !== false, $result ? 'Set added successfully' : 'Failed to add set');
                    break;

            default:
                sendXMLResponse(false, 'Invalid action');
                break;
        }
        break;

    case 'GET':
        $action = $_GET['action'] ?? '';
        switch($action) {
            case 'getAnnoncesForArmor':
                if (!isLoggedIn()) {
                    sendXMLResponse(false, 'Please log in first');
                    break;
                }
            
                $id = $_GET['id'] ?? '';
            
                $set = $articleManager->getSet($id);
                if ($set) {
                    // If the set is valid, send the XML response
                    echo ("Fetched Set: " . print_r($set, true));
                    sendXMLResponse(true, '', ['set' => $set]);
                    break;
                } else {
                    // Handle case where set is not found
                    sendXMLResponse(false, 'Set not found');
                    break;
                }
            
                break;
            
            case 'getArmorNames':
                if (!isLoggedIn()) {
                    sendXMLResponse(false, 'Please log in first');
                    break;
                }
                
                $armorNames = $articleManager->getArmorNames();
                if ($armorNames) {
                    sendXMLResponse(true, '', array('armorNames' => $armorNames));
                    break;
                } else {
                    // Handle case where set is not found
                    sendXMLResponse(false, 'armorNames not found');
                    break;
                }

                break;

            case 'getSourceTypes':

                if (!isLoggedIn()) {
                    sendXMLResponse(false, 'Please log in first');
                    break;
                }

                $sourceTypes = $articleManager->getSourceTypes();
                if ($sourceTypes) {
                    sendXMLResponse(true, 'sourceTypes found', array('sourceTypes' => $sourceTypes));
                    //echo("Source types found: " . print_r($sourceTypes, true));
                    break;
                } else {
                    // Handle case where set is not found
                    //echo("Source types not found: " . print_r($sourceTypes, true));
                    sendXMLResponse(false, 'sourceTypes not found');
                    break;
                }

                break;
        }

    case 'PUT':
        if (!isLoggedIn()) {
            sendXMLResponse(false, 'Please log in first');
            break;
        }

        if (!isAdmin()) {
            sendXMLResponse(false, 'Administrator access required');
            break;
        }

        

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