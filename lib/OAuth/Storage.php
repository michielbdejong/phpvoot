<?php

class Storage {

    private $_pdo;

    public function __construct(PDO $p) {
        $this->_pdo = $p;
    }

    public function getClient($clientId) {
        $stmt = $this->_pdo->prepare("SELECT * FROM Client WHERE id = :client_id");
        $stmt->bindValue(":client_id", $clientId, PDO::PARAM_STR);
        $result = $stmt->execute();
        if (FALSE === $result) {
            return FALSE;
        }
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function storeApprovedScope($clientId, $resourceOwner, $scope) {
        $stmt = $this->_pdo->prepare("INSERT INTO Approval (client_id, resource_owner_id, scope) VALUES(:client_id, :resource_owner_id, :scope)");
        $stmt->bindValue(":client_id", $clientId, PDO::PARAM_STR);
        $stmt->bindValue(":resource_owner_id", $resourceOwner, PDO::PARAM_STR);
        $stmt->bindValue(":scope", $scope, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function getApprovedScope($clientId, $resourceOwner) {
        $stmt = $this->_pdo->prepare("SELECT * FROM Approval WHERE client_id = :client_id AND resource_owner_id = :resource_owner_id");
        $stmt->bindValue(":client_id", $clientId, PDO::PARAM_STR);
        $stmt->bindValue(":resource_owner_id", $resourceOwner, PDO::PARAM_STR);
        $result = $stmt->execute();
        if (FALSE === $result) {
            return FALSE;
        }
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function generateAccessToken($clientId, $resourceOwner, $scope) {
        $accessToken = Random::hex(16);
        $stmt = $this->_pdo->prepare("INSERT INTO AccessToken (client_id, resource_owner_id, issue_time, expires_in, scope, access_token) VALUES(:client_id, :resource_owner_id, :issue_time, :expires_in, :scope, :access_token)");
        $stmt->bindValue(":client_id", $clientId, PDO::PARAM_STR);
        $stmt->bindValue(":resource_owner_id", $resourceOwner, PDO::PARAM_STR);
        $stmt->bindValue(":issue_time", time(), PDO::PARAM_INT);
        $stmt->bindValue(":expires_in", 3600, PDO::PARAM_INT);
        $stmt->bindValue(":scope", $scope, PDO::PARAM_STR);
        $stmt->bindValue(":access_token", $accessToken, PDO::PARAM_STR);
        return ($stmt->execute()) ? $accessToken : FALSE;
    }

    public function getAccessToken($accessToken) {
        $stmt = $this->_pdo->prepare("SELECT * FROM AccessToken WHERE access_token = :access_token");
        $stmt->bindValue(":access_token", $accessToken, PDO::PARAM_STR);
        $result = $stmt->execute();
        if (FALSE === $result) {
            return FALSE;
        }
        return $stmt->fetch(PDO::FETCH_OBJ);        
    }

    public function generateAuthorizeNonce($clientId, $resourceOwner) {
        $authorizeNonce = Random::hex(16);
        $stmt = $this->_pdo->prepare("INSERT INTO AuthorizeNonce (client_id, resource_owner_id, authorize_nonce) VALUES(:client_id, :resource_owner_id, :authorize_nonce)");
        $stmt->bindValue(":client_id", $clientId, PDO::PARAM_STR);
        $stmt->bindValue(":resource_owner_id", $resourceOwner, PDO::PARAM_STR);
        $stmt->bindValue(":authorize_nonce", $authorizeNonce, PDO::PARAM_STR);
        return ($stmt->execute()) ? $authorizeNonce : FALSE;
    }

    public function getAuthorizeNonce($clientId, $resourceOwner, $authorizeNonce) {
        $stmt = $this->_pdo->prepare("SELECT * FROM AuthorizeNonce WHERE client_id = :client_id AND authorize_nonce = :authorize_nonce AND resource_owner_id = :resource_owner_id");
        $stmt->bindValue(":client_id", $clientId, PDO::PARAM_STR);
        $stmt->bindValue(":resource_owner_id", $resourceOwner, PDO::PARAM_STR);
        $stmt->bindValue(":authorize_nonce", $authorizeNonce, PDO::PARAM_STR);
    
        $result = $stmt->execute();
        if (FALSE === $result) {
            return FALSE;
        }
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

}

?>
