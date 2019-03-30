<?php
namespace app\modules\v1\controllers;

use app\modules\v1\components\Response\ResponseFactory;
use app\modules\v1\models\Credential;
use Yii;
use yii\web\Controller;
use yii\web\UnauthorizedHttpException;

/**
 * Class BaseController
 *
 * @package common\controllers
 *
 * @property ResponseFactory $response
 * @property Credential $credential
 * @property array $body
 * @property string $rawBody
 * @property array $headers
 * @property array $query
 */
class BaseController extends Controller
{
    /** @var array $body */
    private $body;

    /** @var string $rawBody */
    private $rawBody;

    /** @var array $headers */
    private $headers;

    /** @var array $query */
    private $query;

    /** @var ResponseFactory $response */
    private $response;

    /** @var Credential $credential */
    private $credential;

    public function guestActions()
    {
        return ['v1/credential/auth'];
    }

    public function init(){
        $this->response = new ResponseFactory();

        $this->body = json_decode(Yii::$app->getRequest()->getRawBody(), true);
        $this->rawBody = Yii::$app->getRequest()->getRawBody();
        $this->headers = Yii::$app->getRequest()->getHeaders();
        $this->query = Yii::$app->getRequest()->get();
        
        if($this->requiresAuth()){
            $this->authenticate();
        }

    }

    /**
     * Enables rate limit and disables it in the response headers.
     *
     * @return array
     */
    public function behaviors() {
        return [
            'rateLimiter' => [
                'class' => 'yii\filters\RateLimiter',
                'enableRateLimitHeaders' => false
            ]
        ];
    }


    /**
     * Verifies if it's an endpoint that requires auth.
     *
     * @return bool
     */
    private function requiresAuth()
    {
        $current_route = Yii::$app->requestedRoute ?? null;

        if (empty($current_route)) {
            return true;
        }

        if (in_array($current_route, $this->guestActions())) {
            return false;
        }

        return true;
    }


    /**
     * Validates JWT and find the credential associated in it.
     *
     * @throws UnauthorizedHttpException
     */
    private function authenticate()
    {
        $token = Yii::$app->getRequest()->getHeaders()->get('Authorization') ?? null;

        if(empty($token)){
            throw new UnauthorizedHttpException();
        }

        $this->credential = Credential::findIdentityByAccessToken($token);

        Yii::$app->user->setIdentity($this->credential);
    }

    /**
     * @return Credential
     */
    public function getCredential(): Credential
    {
        return $this->credential;
    }

    /**
     * @return ResponseFactory
     */
    public function getResponse(): ResponseFactory
    {
        return $this->response;
    }

    /**
     * @param string $arg
     * @return null|string
     */
    protected function getBody(string $arg): ?string{
        return $this->body[$arg] ?? null;
    }

    /**
     * @return null|string
     */
    protected function getRawBody(): ?string{
        return $this->rawBody ?? null;
    }

    /**
     * @param string $arg
     * @return null|string
     */
    protected function getHeaders(string $arg): ?string{
        return $this->headers[$arg] ?? null;
    }

    /**
     * @param string $arg
     * @return null|string
     */
    protected function getQuery(string $arg): ?string{
        return $this->query[$arg] ?? null;
    }



}