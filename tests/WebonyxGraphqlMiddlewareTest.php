<?php

namespace PsCs;
use PHPUnit\Framework\TestCase;
use GraphQL\Server\StandardServer;

use Psr\Http\Message\{
    ServerRequestInterface as Request,
    ResponseInterface as Response
  };
use PsCs\Psr7\Middleware\Graphql\WebonyxGraphqlMiddleware;
use Interop\Http\ServerMiddleware\DelegateInterface;
class WebonyxGraphqlMiddlewareTest extends TestCase {

    public function getMockHandler() {

        return $this->createMock(StandardServer::class);

    }
    public function getMockPsr7RequestGet() {
        $request = $this->createMock(Request::class);
    /**
     * This should not be used (adding this in order to wait webonyx release a new version where variables params is not required)
     */
       /** ->setMethods(['getQueryParams'])
        ->getMock();**/
        return $request;
    }

    public function getMockDelegateInterface() {
        return $this->createMock(DelegateInterface::class);
    }

    public function testDefaultProcessGetRequest(): void {
        /**
         * Create request
         */
        $request = $this->getMockPsr7RequestGet();
        $uri =  $this->createMock(\Psr\Http\Message\UriInterface::class);
        $uri->expects($this->any())->method("getPath")->willReturn("/graphql");
        $request->expects($this->any())
        ->method('getUri')->willReturn($uri);
        
        $request->expects($this->any())
        ->method('withQueryParams')->willReturn($request);

        $request->expects($this->any())
        ->method('getMethod')->willReturn("get");
        
        /**
         * Create the StandardServer provider by webonyx
         */
        $handler = $this->getMockHandler();
        $handler->expects($this->once())
        ->method('executePsrRequest')
        ->with($request)->willReturn($this->createMock(Response::class));
        
        /**
         * Create the DelegateInterface
         */
        $delegate = $this->getMockDelegateInterface();
        $delegate
        ->expects($this->never())
        ->method('process');

        $middleware = new WebonyxGraphqlMiddleware($handler);
        $this->assertInstanceOf(Response::class, $middleware->process($request, $delegate));

    }

    public function testDefaultProcessPostRequest(): void {
        /**
         * Create request
         */
        $request = $this->createMock(Request::class);
        $uri =  $this->createMock(\Psr\Http\Message\UriInterface::class);
        $uri->expects($this->any())->method("getPath")->willReturn("/graphql");
        $request->expects($this->any())
        ->method('getUri')->willReturn($uri);
        
        $request->expects($this->any())
        ->method('withParsedBody')->willReturn($request);

        $request->expects($this->any())
        ->method('getMethod')->willReturn("post");
        
        /**
         * Create the StandardServer provider by webonyx
         */
        $handler = $this->getMockHandler();
        $handler->expects($this->once())
        ->method('executePsrRequest')
        ->with($request)->willReturn($this->createMock(Response::class));
        
        /**
         * Create the DelegateInterface
         */
        $delegate = $this->getMockDelegateInterface();
        $delegate
        ->expects($this->never())
        ->method('process');

        $middleware = new WebonyxGraphqlMiddleware($handler);
        $this->assertInstanceOf(Response::class, $middleware->process($request, $delegate));

    }


    public function testDefaultProcessPostRequestViaEmptyHeader(): void {
        /**
         * Create request
         */
        $request = $this->createMock(Request::class);
        $uri =  $this->createMock(\Psr\Http\Message\UriInterface::class);
        $uri->expects($this->any())->method("getPath")->willReturn("/");
        $request->expects($this->any())
        ->method('getUri')->willReturn($uri);
        
        $request->expects($this->any())
        ->method('withParsedBody')->willReturn($request);

        $request->expects($this->any())
        ->method('getMethod')->willReturn("post");
        
        $request->expects($this->any())->method("hasHeader")->with("content-type")->willReturn(false);

        $request->expects($this->never())->method("getHeaderLine");
        /**
         * Create the StandardServer provider by webonyx
         */
        $handler = $this->getMockHandler();
        $handler->expects($this->never())->method('executePsrRequest');
        
        /**
         * Create the DelegateInterface
         */
        $delegate = $this->getMockDelegateInterface();
        $delegate
        ->expects($this->once())
        ->method('process')->willReturn($this->createMock(Response::class));

        $middleware = new WebonyxGraphqlMiddleware($handler);
        $this->assertInstanceOf(Response::class, $middleware->process($request, $delegate));

    }

    public function testDefaultProcessPostRequestViaRightHeader(): void {
        /**
         * Create request
         */
        $request = $this->createMock(Request::class);
        $uri =  $this->createMock(\Psr\Http\Message\UriInterface::class);
        $uri->expects($this->any())->method("getPath")->willReturn("/");
        $request->expects($this->any())
        ->method('getUri')->willReturn($uri);
        
        $request->expects($this->any())
        ->method('withParsedBody')->willReturn($request);

        $request->expects($this->any())
        ->method('getMethod')->willReturn("post");
        
        $request->expects($this->any())->method("hasHeader")->with("content-type")->willReturn(true);

        $request->expects($this->any())->method("getHeaderLine")->willReturn("text/html , application/graphql");
        /**
         * Create the StandardServer provided by webonyx
         */
        $handler = $this->getMockHandler();
        $handler->expects($this->once())->method('executePsrRequest')
        ->with($request)->willReturn($this->createMock(Response::class));;
        
        /**
         * Create the DelegateInterface
         */
        $delegate = $this->getMockDelegateInterface();
        $delegate
        ->expects($this->never())
        ->method('process');

        $middleware = new WebonyxGraphqlMiddleware($handler);
        $this->assertInstanceOf(Response::class, $middleware->process($request, $delegate));

    }

    public function testDefaultProcessPostRequestViaWrongHeader(): void {
        /**
         * Create request
         */
        $request = $this->createMock(Request::class);
        $uri =  $this->createMock(\Psr\Http\Message\UriInterface::class);
        $uri->expects($this->any())->method("getPath")->willReturn("/");
        $request->expects($this->any())
        ->method('getUri')->willReturn($uri);
        
        $request->expects($this->any())
        ->method('withParsedBody')->willReturn($request);

        $request->expects($this->any())
        ->method('getMethod')->willReturn("post");
        
        $request->expects($this->any())->method("hasHeader")->with("content-type")->willReturn(true);

        $request->expects($this->any())->method("getHeaderLine")->willReturn("text/html");
        /**
         * Create the StandardServer provided by webonyx
         */
        $handler = $this->getMockHandler();
        $handler->expects($this->never())->method('executePsrRequest');
        
        /**
         * Create the DelegateInterface
         */
        $delegate = $this->getMockDelegateInterface();
        $delegate
        ->expects($this->once())
        ->method('process')->willReturn($this->createMock(Response::class));

        $middleware = new WebonyxGraphqlMiddleware($handler);
        $this->assertInstanceOf(Response::class, $middleware->process($request, $delegate));

    }

}