<?php

namespace RedirectsResourceTest\V1\Rest\Redirects;

use Unir\V1\Rest\Redirects\RedirectsResource;
use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\TableGateway;
use Zend\Stdlib\Parameters;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class RedirectResourceTest extends AbstractHttpControllerTestCase
{
    /** @var  RedirectsResource $resource */
    protected $resource;

    /** @var  TableGateway $table */
    protected $table;

    protected $data = [
        1 => [ 'id' => 1, 'origin' => 'http://www.yivoff.com/oldurl/', 'target' => 'http://www.yivoff.com/newurl/', 'owner' => 1, 'redirect_type' => 1, 'redirect_code' => '301', 'active' => 1],
        [ 'id' => 2, 'origin' => 'http://www.yivoff.com/oldurl2/', 'target' => 'http://www.yivoff.com/newurl/', 'owner' => 1, 'redirect_type' => 1, 'redirect_code' => '301', 'active' => 1],
        [ 'id' => 3, 'origin' => 'http://www.yivoff.com/oldurl3/', 'target' => 'http://www.yivoff.com/newurl/', 'owner' => 1, 'redirect_type' => 1, 'redirect_code' => '301', 'active' => 1],
        [ 'id' => 4, 'origin' => 'http://www.yivoff.com/oldurl4/', 'target' => 'http://www.yivoff.com/newurl/', 'owner' => 1, 'redirect_type' => 1, 'redirect_code' => '301', 'active' => 0],
        [ 'id' => 5, 'origin' => 'http://www.example.com/oldurl/', 'target' => 'http://www.example.net/newurl/', 'owner' => 1, 'redirect_type' => 1, 'redirect_code' => '301', 'active' => 1],
        [ 'id' => 6, 'origin' => 'http://www.example.com/foo/bar', 'target' => 'http://www.example.net/bar/foo/', 'owner' => 2, 'redirect_type' => 1, 'redirect_code' => '301', 'active' => 1],
        [ 'id' => 7, 'origin' => 'http://www.example.com/foo/', 'target' => 'http://www.example.net/bat/', 'owner' => 2, 'redirect_type' => 2, 'redirect_code' => '301', 'active' => 1],
        [ 'id' => 8, 'origin' => 'http://subdomain.domain.com/img/', 'target' => 'http://s.subdomain.domain.com/imgs/', 'owner' => 3, 'redirect_type' => 3, 'redirect_code' => '302', 'active' => 1],
        [ 'id' => 9, 'origin' => 'http://subdomain.domain.com/old/moore.php', 'target' => 'http://subdomain.domain.com/path/newapp/', 'owner' => 2, 'redirect_type' => 1, 'redirect_code' => '302', 'active' => 1],
        [ 'id' => 10, 'origin' => 'http://whoiswho.co.uk/batman', 'target' => 'http://whoiswho.co.uk/brucewayne', 'owner' => 2, 'redirect_type' => 1, 'redirect_code' => '301', 'active' => 0],
        [ 'id' => 11, 'origin' => 'http://www.yivoff.com/', 'target' => 'http://www.yivoff.es/', 'owner' => 3, 'redirect_type' => 3, 'redirect_code' => '301', 'active' => 1],
    ];


    public function setUp()
    {
        // Yup. Much to learn, this one has.
        $this->setApplicationConfig(include(dirname(__FILE__) . '/../../../../../../config/application.config.php'));

        $dsn = $GLOBALS['DB_DSN'] . ';' . $GLOBALS['DB_DBNAME'];

        // Adapter, TG, and RedirectResource (actual test)
        $zf_adapter     = new Adapter([
            'driver' => 'pdo',
            'dsn'    => $dsn,
            'user'   => $GLOBALS['DB_USER'],
            'pass'   => $GLOBALS['DB_PASSWD'],
        ]);
        $this->table    = new TableGateway('redirects', $zf_adapter);
        $this->resource = new RedirectsResource($this->table, 'id', 'Zend\Paginator\Paginator');

        // getting the data where it belongs.
        foreach ($this->data as $row) {
            $this->table->insert($row);
        }

        parent::setUp();
    }

    public function tearDown()
    {
        // Getting rid of the data.
        $this->table->getAdapter()->query('TRUNCATE TABLE redirects')->execute();
        parent::tearDown();
    }

    public function testGetId5()
    {

        // we fetch the fifth
        $result = $this->resource->fetch(5)->getArrayCopy();

        // got the fifth
        $this->assertEquals($this->data[5]['origin'], $result['origin']);
    }

    public function testFetchAllFilteredOrigin()
    {
        $busqueda = "example";

        $params = new Parameters(['origin' => $busqueda, 'precise_origin' => false]);

        $result = $this->resource->fetchAll($params);
        $total  = count(array_filter($this->data, function ($element) use ($busqueda) {
            return preg_match("|.+$busqueda.+|", $element['origin']);
        }));

        // should have three
        $count = $result->getCurrentItemCount();

        $this->assertEquals($total, $count);
    }

    public function testFetchAllFilteredOriginEmpty()
    {
        // in the DB, but not active.
        $busqueda = 'http://whoiswho.co.uk/batman';

        // "precise" filter will filter out our one hit wonder
        $params = new Parameters(['origin' => $busqueda, 'precise_origin' => true]);
        $result = $this->resource->fetchAll($params);
        $total  = count(array_filter($this->data, function ($element) use ($busqueda) {
            return preg_match("|^$busqueda$|", $element['origin']) && $element['active'] === 1;
        }));

        $count = $result->getCurrentItemCount();

        // should return 000
        $this->assertEquals($total, $count);
    }

    public function testFetchAllFilteredOriginPreciseMulti()
    {

        $busqueda = 'http://www.yivoff.com/oldurl/';

        $params = new Parameters(['origin' => $busqueda, 'precise_origin' => true]);
        $result = $this->resource->fetchAll($params);
        $mios   = array_filter($this->data, function ($element) use ($busqueda) {
            return (
                       preg_match("|^$busqueda$|", $element['origin'])
                       ||
                       preg_match("|^" . $element['origin'] . ".?|", $busqueda)
                   )
                   && $element['active'] === 1;
        });

        // should return 2:
        $count = $result->getTotalItemCount();

        $this->assertEquals(count($mios), $count);
    }



    public function testFetchAll()
    {
        $total  = count($this->data);
        $result = $this->resource->fetchAll();
        $count  = $result->getTotalItemCount();

        $this->assertEquals($count, $total);


    }

    public function testCreatePartial()
    {
        $input        = ['origin' => 'http://geocities.com/mynew_homepage', 'target' => 'http://altavista.com/123'];
        $params       = new Parameters($input);
        $new_redirect = $this->resource->create($params)->getArrayCopy();
        $this->assertEquals($new_redirect['origin'], $input['origin']);
        $this->assertEquals($new_redirect['redirect_code'], 301);
    }

    public function testCreateFull()
    {
        $input        = [
            'origin'        => 'http://geocities.com/mynew_homepage',
            'target'        => 'http://altavista.com/123',
            'redirect_code' => 302,
            'redirect_type' => 1,
            'owner'         => 7,
            'active'        => 1,
        ];
        $params       = new Parameters($input);
        $new_redirect = $this->resource->create($params)->getArrayCopy();

        $this->assertTrue(
            $new_redirect['owner'] == $input['owner'] &&
            $new_redirect['redirect_type'] == $input['redirect_type'] &&
            $new_redirect['target'] == $input['target'] &&
            $new_redirect['origin'] == $input['origin']
        );
    }

    public function testUpdate()
    {

        $input    = ['origin' => 'http://www.yahoo.com',];
        $id       = 6;
        $params   = new Parameters($input);
        $response = $this->resource->update($id, $params)->getArrayCopy();

        $this->assertEquals($input['origin'], $response['origin']);

    }
}