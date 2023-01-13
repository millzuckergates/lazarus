<?php

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Psr7\Utils;
use GuzzleHttp\Exception\GuzzleException;
use Phalcon\Http\Message\Request;

/**
 * Interface d'accès à GitHub
 */
class GitHub {

    /**
     * @var HttpClient Client HTTP
     */
    private HttpClient $httpClient;

    /**
     * @var string Url du projet sur GitHub
     */
    private string $urlProjet = "https://api.github.com/repos/SyrinxAndCo/lazarus";

    /**
     * @var array|string[] Header par défaut pour les appels à l'API GitHub
     */
    private array $defaultHeader = [
        "Accept" => "application/vnd.github.v3+json",
        "Authorization" => "token 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"
    ];

    /**
     * Constructeur
     */
    public function __construct() {
        $this->httpClient = new HttpClient();
    }

    /**
     * Creer une issue dans le projet
     * @param string $titre Titre du bug
     * @param string $contenu Description du bug
     * @return int
     */
    public function creerIssue(string $titre, string $contenu): int {
        $postData = json_encode((object) [
            "title" => $titre,
            "body" => $contenu
        ]);
        $request = new Request(
          'POST',
          $this->urlProjet . '/issues',
          Utils::streamFor($postData),
          $this->defaultHeader
        );
        try {
            $this->httpClient->send($request);
            return 0;
        } catch (GuzzleException $e) {
            $this->afficheErreur($e);
            return 1;
        }
    }

    /**
     * Récupère la liste des issues du projet
     * @return array
     */
    public function getIssues(): array {
        $request = new Request(
          'GET',
          $this->urlProjet . '/issues',
          "php://memory",
          $this->defaultHeader
        );
        try {
            $response = $this->httpClient->send($request);
            return json_decode($response->getBody());
        } catch (GuzzleException $e) {
            $this->afficheErreur($e);
            die;
        }
    }

    /**
     * Upload un fichier directement dans le dépôt sur la branche des issues
     * @param string $nomFichier Nom du fichier à uploader
     * @param string $contenu Contenu du fichier à uploader en base64
     * @param string $message Message du commit lors de l'upload
     * @return string
     */
    public function ajouterImage(string $nomFichier, string $contenu, string $message): string {
        $payload = json_encode((object) ["message" => $message, "branch" => "imagesIssues", "content" => $contenu]);
        $request = new Request(
          'PUT',
          $this->urlProjet . '/contents/' . $nomFichier,
          Utils::streamFor($payload),
          $this->defaultHeader
        );
        try {
            $response = $this->httpClient->send($request);
            $retour = json_decode($response->getBody());
            if (isset($retour->content->html_url)) {
                return preg_replace("/blob/", "/raw/", $retour->content->html_url);
            }
        } catch (GuzzleException $e) {
            $this->afficheErreur($e);
        }
        return "";
    }

    /**
     * Affiche rapidement le contenu de l'erreur HTTP
     * @param GuzzleException $e
     */
    private function afficheErreur(GuzzleException $e) {
        echo "Oups...<br/>";
        echo "Status : " . $e->getCode() . "<br/>";
        echo "Message : " . $e->getMessage();
    }
}