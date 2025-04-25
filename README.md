# Coze SDK for PHP
[Coze API 官方文档](https://www.coze.cn/open/docs/developer_guides/coze_api_overview)
Coze SDK for PHP 是 Coze API 的 PHP 语言 SDK，提供了对 Coze API 的封装，方便开发者在 PHP 中使用 Coze API。

## 准备工作
1. 创建新应用； 客户端类型 一定选 “服务类应用”、“服务类应用”、“服务类应用”
2. 获取 公钥、私钥 和应用ID
3. 创建工作流，并发布；获取工作流ID

### 获取Token

```php
use GuzzleHttp\Client;
use Coze\Auth\OAuthClient;

$appId = 'your_app_id'; // 应用ID
$publicKey = 'your_app_public_key'; // 公钥指纹
$privateKey = file_get_contents('./private_key.pem'); // 私钥

$client = new Client([
            'base_uri' => 'https://api.coze.cn',
            'timeout' => 5,
        ]);

$oauth = new OAuthClient($clientId, $publicKey, $clientSecret, $client);

$oauth->getAccessToken();
/**
 * [
 *  'expires_in' => 1000000000, 过期时间戳
 *  'access_token' => 'xxxxx'
 * ]
 */
```

### 工作流调用
[详细文档](https://www.coze.cn/open/docs/developer_guides/workflow_run)
```php
use Coze\Workflow\Run;

$workflowId = 10002312312; // 工作流ID

$run = new Run($client, $workflowId);

$params = []; // 工作流中自定义的传参内容
$run->handle($params)

```