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

    protected $data;


    /**
     * Prepping up the scenery
     *
     */
    public function setUp()
    {
        $this->data = include '_files/data.php';

        $dsn = $GLOBALS['DB_DSN'] . ';' . $GLOBALS['DB_DBNAME'];

        // Adapter, TG, and RedirectResource (actual test)
        $zf_adapter     = new Adapter([
            'driver' => 'pdo',
            'dsn' => $dsn,
            'user' => $GLOBALS['DB_USER'],
            'pass' => $GLOBALS['DB_PASSWD'],
        ]);
        $this->table    = new TableGateway('redirects', $zf_adapter);
        $this->resource = new RedirectsResource($this->table, 'id', 'Zend\Paginator\Paginator');

        // getting the data where it belongs.
        foreach ($this->data as $row) {
            $this->table->insert($row);
        }

        parent::setUp();
    }

    /**
     * The center cannot hold.
     */
    public function tearDown()
    {
        // Getting rid of the data.
        $this->table->getAdapter()->query('TRUNCATE TABLE redirects')->execute();
        parent::tearDown();
    }

    /**
     * Pruebo pedir un id y recibir los datos que espero
     */
    public function testGetId5()
    {

        // we fetch the fifth
        $result = $this->resource->fetch(5)->getArrayCopy();

        // got the fifth
        $this->assertEquals($this->data[5]['origin'], $result['origin']);
    }

    /**
     * Pruebo una búsqueda filtrada por "example" en "origen", y debiera recibir 3 resultados
     */
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

    /**
     * Una búsqueda "precisa" que no devuelve resultados porque el url buscado está desactivado
     */
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

    /**
     * La busqueda "precisa" puede devolver un conjunto de dos urls: el directo y una super-regla que lo contenga
     */
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


    /**
     *  Fetch all sin parámetros debiera devolver tantos resultados como insertamos en la DB
     */
    public function testFetchAll()
    {
        $total  = count($this->data);
        $result = $this->resource->fetchAll();
        $count  = $result->getTotalItemCount();

        $this->assertEquals($count, $total);


    }

    /**
     * Verificamos que los valores por defecto funcionan correctamente
     * @todo: fixme: aunque por ahora esta "lógica" está en la DB)
     */
    public function testCreatePartial()
    {
        $input        = ['origin' => 'http://geocities.com/mynew_homepage', 'target' => 'http://altavista.com/123'];
        $params       = new Parameters($input);
        $new_redirect = $this->resource->create($params)->getArrayCopy();
        $this->assertEquals($new_redirect['origin'], $input['origin']);
        $this->assertEquals($new_redirect['redirect_code'], 301);
    }

    /**
     * Creación con todos los datos
     */
    public function testCreateFull()
    {
        $input        = [
            'origin' => 'http://geocities.com/mynew_homepage',
            'target' => 'http://altavista.com/123',
            'redirect_code' => 302,
            'redirect_type' => 1,
            'owner' => 7,
            'active' => 1,
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

    /**
     * Actualizar actualiza
     */
    public function testUpdate()
    {

        $input    = ['origin' => 'http://www.yahoo.com',];
        $id       = 6;
        $params   = new Parameters($input);
        $response = $this->resource->update($id, $params)->getArrayCopy();

        $this->assertEquals($input['origin'], $response['origin']);

    }
}