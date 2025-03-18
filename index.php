<?php
require 'vendor/autoload.php';
session_start();

$client = new Google_Client();
$client->setClientId('1069755973942-ikac71jt20befitl5delvlrna73a0ofk.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/index.php');
$client->addScope(Google_Service_PeopleService::CONTACTS_READONLY);

if (!isset($_GET['code']) && !isset($_SESSION['access_token'])) {
    $auth_url = $client->createAuthUrl();
    echo "<a href='$auth_url'>Iniciar sesión con Google</a>";
    exit;
}

if (isset($_GET['code'])) {
    $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $_SESSION['access_token'] = $client->getAccessToken();
    header('Location: index.php');
    exit;
}

if (isset($_SESSION['access_token'])) {
    $client->setAccessToken($_SESSION['access_token']);
    $service = new Google_Service_PeopleService($client);

    $contacts = $service->people_connections->listPeopleConnections('people/me', [
        'pageSize' => 10,
        'personFields' => 'names,emailAddresses,phoneNumbers'
    ]);

    echo "<h2>Lista de Contactos</h2>";
    foreach ($contacts->getConnections() as $contact) {
        $name = 'Sin Nombre';
        $email = 'Sin Email';

        if (!empty($contact->getNames())) {
            $name = $contact->getNames()[0]->getDisplayName();
        }

        if (!empty($contact->getEmailAddresses())) {
            $email = $contact->getEmailAddresses()[0]->getValue();
        }

        echo "<p><strong>$name</strong> - $email</p>";
    }

    echo '<br><a href="?logout=true">Cerrar sesión</a>';
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}
?>