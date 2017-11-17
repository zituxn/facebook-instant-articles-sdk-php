<?hh

namespace Facebook\InstantArticles\Client;

class ClientExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testExtendsException(): void
    {
        $exception = new ClientException();

        $this->assertInstanceOf('Exception', $exception);
    }
}
