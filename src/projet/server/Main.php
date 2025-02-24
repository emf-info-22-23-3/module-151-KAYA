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
            $set = $data['set'];
            echo "  <setWanted>\n";
            foreach ($set as $key => $value) {
                echo "    <" . htmlspecialchars($key, ENT_XML1, 'UTF-8') . ">" 
                     . htmlspecialchars($value, ENT_XML1, 'UTF-8') 
                     . "</" . htmlspecialchars($key, ENT_XML1, 'UTF-8') . ">\n";
            }

            // Handle sources inside the set
            foreach (['CapSource', 'TunicSource', 'TrousersSource'] as $sourceKey) {
                if (isset($data[$sourceKey])) {
                    echo "    <" . htmlspecialchars($sourceKey, ENT_XML1, 'UTF-8') . ">\n";
                    foreach ($data[$sourceKey] as $key => $value) {
                        echo "      <" . htmlspecialchars($key, ENT_XML1, 'UTF-8') . ">" 
                             . htmlspecialchars($value, ENT_XML1, 'UTF-8') 
                             . "</" . htmlspecialchars($key, ENT_XML1, 'UTF-8') . ">\n";
                    }
                    echo "    </" . htmlspecialchars($sourceKey, ENT_XML1, 'UTF-8') . ">\n";
                }
            }
            echo "  </setWanted>\n";
        }

        // Proper handling of sourceTypes
        if (isset($data['sourceTypes']) && is_array($data['sourceTypes']) && count($data['sourceTypes']) > 0) {
            echo "  <sourceTypes>\n";
            foreach ($data['sourceTypes'] as $item) {
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
            echo "  </sourceTypes>\n";
        }

        // Handling armor data
        foreach ($data as $key => $items) {
            if ($key !== 'set' && $key !== 'sourceTypes' && !in_array($key, ['CapSource', 'TunicSource', 'TrousersSource'])) {
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
                echo "  </" . htmlspecialchars($key, ENT_XML1, 'UTF-8') . ">\n";
            }
        }
    }

    echo "</response>";
    exit;
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

                case 'beginTransaction':
                    if (!isLoggedIn()) {
                        sendXMLResponse(false, 'Please log in first');
                        break;
                    }
            
                    if (!isAdmin()) {
                        sendXMLResponse(false, 'Administrator access required');
                        break;
                    }
            
                    $result = $articleManager->beginTransaction();
            
                    if ($result) {
                        sendXMLResponse(true, 'Transaction has begun!');
                    } else {
                        sendXMLResponse(false, 'Failed to start the transaction');
                    }
                    break;
            
                case 'commitTransaction':
                    if (!isLoggedIn()) {
                        sendXMLResponse(false, 'Please log in first');
                        break;
                    }
            
                    if (!isAdmin()) {
                        sendXMLResponse(false, 'Administrator access required');
                        break;
                    }
            
                    $result = $articleManager->commitTransaction();
            
                    if ($result) {
                        sendXMLResponse(true, 'Transaction has been committed!');
                    } else {
                        sendXMLResponse(false, 'Failed to commit the transaction');
                    }
                    break;
            
                case 'rollbackTransaction':
                    if (!isLoggedIn()) {
                        sendXMLResponse(false, 'Please log in first');
                        break;
                    }
            
                    if (!isAdmin()) {
                        sendXMLResponse(false, 'Administrator access required');
                        break;
                    }
            
                    $result = $articleManager->rollbackTransaction();
            
                    if ($result) {
                        sendXMLResponse(true, 'Transaction has been rolled back!');
                    } else {
                        sendXMLResponse(false, 'Failed to roll back the transaction');
                    }
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

                    $setData = array(
                        'id' => $set->getPkSet(),
                        'name' => $set->getNom(),
                        'cap_name' => $set->getCapNom(),
                        'tunic_name' => $set->getTunicNom(),
                        'trousers_name' => $set->getTrousersNom(),
                        'description' => $set->getDescription(),
                        'effect' => $set->getEffet(),
                        'cap_source' => $set->getFkCapSource(),
                        'tunic_source' => $set->getFkTunicSource(),
                        'trousers_source' => $set->getFkTrousersSource(),
                        'image' => $set->getImageSet()
                    );

                    // Retrieve the Source objects using foreign keys (assuming FK_Type_Source for each)
                    $CapSource = $articleManager->readSourceByID($set->getFkCapSource()); 
                    $TunicSource = $articleManager->readSourceByID($set->getFkTunicSource()); 
                    $TrousersSource = $articleManager->readSourceByID($set->getFkTrousersSource()); 
            
                    // Prepare the response array with the set and source objects
                    $response = [
                        'set' => $setData,
                        'CapSource' => $CapSource ? [
                            'id' => $CapSource->getId(),
                            'source' => $CapSource->getSource(),
                            'type_source' => $CapSource->getTypeSourceId()
                        ] : null,
                        'TunicSource' => $TunicSource ? [
                            'id' => $TunicSource->getId(),
                            'source' => $TunicSource->getSource(),
                            'type_source' => $TunicSource->getTypeSourceId()
                        ] : null,
                        'TrousersSource' => $TrousersSource ? [
                            'id' => $TrousersSource->getId(),
                            'source' => $TrousersSource->getSource(),
                            'type_source' => $TrousersSource->getTypeSourceId()
                        ] : null
                    ];                    
            
                    // Send the response with the set and the source objects
                    sendXMLResponse(true, '', $response);
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

        // Collecting form data
        $armorName = $_POST['armorName'] ?? '';
        $armorCapName = $_POST['armorCapName'] ?? '';
        $armorTunicName = $_POST['armorTunicName'] ?? '';
        $armorTrousersName = $_POST['armorTrousersName'] ?? '';
        $armorEffect = $_POST['armorEffect'] ?? '';
        $armorDescription = $_POST['armorDescription'] ?? '';
        $armorCapSourceType = $_POST['armorCapSourceType'] ?? '';
        $armorCapSource = $_POST['armorCapSource'] ?? '';
        $armorTunicSourceType = $_POST['armorTunicSourceType'] ?? '';
        $armorTunicSource = $_POST['armorTunicSource'] ?? '';
        $armorTrousersSourceType = $_POST['armorTrousersSourceType'] ?? '';
        $armorTrousersSource = $_POST['armorTrousersSource'] ?? '';

        // Handling the uploaded image (if exists)
        if (isset($_FILES['armorImage']) && $_FILES['armorImage']['error'] === UPLOAD_ERR_OK) {
            $imageTempPath = $_FILES['armorImage']['tmp_name'];
            $imageName = $_FILES['armorImage']['name'];
            $imagePath = 'uploads/' . $imageName;
            move_uploaded_file($imageTempPath, $imagePath);
        } else {
            // If no new image, retain the old image path or set to '' if no image exists
            $imagePath = $_POST['existingImagePath'] ?? ''; // Assuming the old image path is sent
        }

        // Fetch and update sources (Cap, Tunic, Trousers)
        $armorCapSourceId = $_POST['armorCapSourceId'] ?? null;
        $armorTunicSourceId = $_POST['armorTunicSourceId'] ?? null;
        $armorTrousersSourceId = $_POST['armorTrousersSourceId'] ?? null;

        // Update sources if necessary
        if ($armorCapSource && $armorCapSourceType) {
            $articleManager->updateSource($armorCapSourceId, $armorCapSource, $armorCapSourceType);
        }
        if ($armorTunicSource && $armorTunicSourceType) {
            $articleManager->updateSource($armorTunicSourceId, $armorTunicSource, $armorTunicSourceType);
        }
        if ($armorTrousersSource && $armorTrousersSourceType) {
            $articleManager->updateSource($armorTrousersSourceId, $armorTrousersSource, $armorTrousersSourceType);
        }

        // Create the Set object with the collected data
        $set = new Set(
            $selectedArmorId,  // Assuming the ID of the set to update
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

        // Update the armor set
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
        
        // Read raw input (DELETE request data)
        $inputData = json_decode(file_get_contents('php://input'), true);
    
        // Collecting form data from the raw input
        $idSet = $inputData['idSet'] ?? '';
        $idCapSource = $inputData['idCapSource'] ?? '';
        $idTunicSource = $inputData['idTunicSource'] ?? '';
        $idTrousersSource = $inputData['idTrousersSource'] ?? '';
    
        if (empty($idSet) || empty($idCapSource) || empty($idTunicSource) || empty($idTrousersSource)) {
            sendXMLResponse(false, 'Missing required parameters');
            break;
        }
    
        // Proceed with the deletion
        $result = $articleManager->deleteSet($idSet, $idCapSource, $idTunicSource, $idTrousersSource);
    
        // Send the response based on the result
        sendXMLResponse($result, $result ? 'Set and associated sources deleted successfully' : 'Failed to delete set');
        break;

    default:
        sendXMLResponse(false, 'Invalid request method');
        break;
}
?>