<?php

namespace App\Livewire;

use Livewire\Component;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\DomCrawler\Crawler;
use Exception;

class LinkPreview extends Component
{
    public $url = '';
    public $title = '';
    public $description = '';
    public $image = ''; // Laissez vide par défaut ici
    public $previewing = false;
    public $error = '';

    // Définir une image par défaut
    public $defaultImage = 'https://via.placeholder.com/400x300.png?text=Pas+d%27image'; // Ou une image stockée localement

    protected $rules = [
        'url' => 'required|url',
    ];

    public function mount()
    {
        // S'assurer que l'image est vide au démarrage
        $this->image = '';
    }

    public function updatedUrl($value)
    {
        $this->reset(['title', 'description', 'image', 'error']); // Réinitialise l'image aussi
        $this->previewing = false;
        // Si l'URL est vide, réinitialise l'image à vide pour cacher la carte
        if (empty($value)) {
            $this->image = '';
        }
    }

    public function getLinkPreview()
    {
        $this->validate();
        $this->reset(['title', 'description', 'image', 'error']);
        $this->previewing = true;

        try {
            $client = new Client();
            $response = $client->request('GET', $this->url, [
                'timeout' => 5,
                'allow_redirects' => true,
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                    'Accept-Language' => 'en-US,en;q=0.5',
                ],
            ]);
            $html = (string) $response->getBody();

            $crawler = new Crawler($html, $this->url);

            // 1. Tenter de récupérer les métadonnées Open Graph (og:)
            $this->title = $this->getMetaContent($crawler, 'og:title');
            $this->description = $this->getMetaContent($crawler, 'og:description');
            $this->image = $this->getMetaContent($crawler, 'og:image');

            // 2. Si Open Graph est incomplet, tenter les Twitter Cards (twitter:)
            if (empty($this->title)) {
                $this->title = $this->getMetaContent($crawler, 'twitter:title');
            }
            if (empty($this->description)) {
                $this->description = $this->getMetaContent($crawler, 'twitter:description');
            }
            if (empty($this->image)) { // Ne pas écraser l'image OG si elle a été trouvée
                $this->image = $this->getMetaContent($crawler, 'twitter:image');
            }

            // 3. Si toujours incomplet, tenter les balises HTML standard
            if (empty($this->title)) {
                $this->title = $crawler->filterXPath('//title')->text('');
            }
            if (empty($this->description)) {
                $this->description = $this->getMetaContent($crawler, 'description');
            }

            // Gérer les URLs d'images relatives et s'assurer qu'elles sont complètes
            if (!empty($this->image)) {
                $this->image = $this->absoluteUrl($this->image, $this->url);
            }

            // Utiliser l'image par défaut SEULEMENT si aucune image n'est trouvée APRÈS toutes les tentatives
            if (empty($this->image)) {
                $this->image = $this->defaultImage;
            }

        } catch (ClientException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            $this->error = "Erreur HTTP ($statusCode) lors de la récupération du lien. Le serveur a répondu avec une erreur.";
            $this->image = $this->defaultImage; // Afficher l'image par défaut en cas d'erreur
        } catch (ConnectException $e) {
            $this->error = "Impossible de se connecter au serveur : Vérifiez l'URL ou votre connexion internet.";
            $this->image = $this->defaultImage; // Afficher l'image par défaut en cas d'erreur
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $statusCode = $e->getResponse()->getStatusCode();
                $this->error = "Erreur de requête HTTP ($statusCode) : " . $e->getMessage();
            } else {
                $this->error = "Erreur de requête HTTP : " . $e->getMessage();
            }
            $this->image = $this->defaultImage; // Afficher l'image par défaut en cas d'erreur
        } catch (Exception $e) {
            $this->error = "Une erreur inattendue est survenue : " . $e->getMessage();
            $this->image = $this->defaultImage; // Afficher l'image par défaut en cas d'erreur
        } finally {
            $this->previewing = false;
        }
    }

    // ... (les fonctions getMetaContent et absoluteUrl restent inchangées)
    protected function getMetaContent(Crawler $crawler, string $property): string
    {
        $node = $crawler->filterXPath(sprintf('//meta[@property="%s" or @name="%s"]', $property, $property))->first();
        return $node->count() > 0 ? $node->attr('content') : '';
    }

    protected function absoluteUrl(string $relativeUrl, string $baseUrl): string
    {
        if (preg_match('/^https?:\/\//i', $relativeUrl)) {
            return $relativeUrl;
        }

        $parsedBase = parse_url($baseUrl);
        $scheme = isset($parsedBase['scheme']) ? $parsedBase['scheme'] . '://' : 'http://';
        $host = isset($parsedBase['host']) ? $parsedBase['host'] : '';
        $port = isset($parsedBase['port']) ? ':' . $parsedBase['port'] : '';
        $basePath = isset($parsedBase['path']) ? dirname($parsedBase['path']) : '';

        if (str_starts_with($relativeUrl, '/')) {
            return $scheme . $host . $port . $relativeUrl;
        }

        return rtrim($scheme . $host . $port . $basePath, '/') . '/' . $relativeUrl;
    }

    public function render()
    {
        return view('livewire.link-preview')->layout('layouts.app');
    }
}
