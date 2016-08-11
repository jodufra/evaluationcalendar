<?php
// This file is part of a local Moodle plugin
//
// You can redistribute it and/or modify it under the terms of the  GNU General Public License
// as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
// This plugin is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
// without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
// See the GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License along with Moodle.
// If not, see <http://www.gnu.org/licenses/>.

/**
 * [File Documentation]
 *
 * @package local_pfc
 * @copyright 2016 Instituto Politécnico de Leiria <http://www.ipleiria.pt>
 * @author Duarte Mateus <2120189@my.ipleiria.pt>
 * @author Joel Francisco <2121000@my.ipleiria.pt>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class local_pfc_webServiceConnector
{

    /**
     * @var \local_pfc\api\calendar_api
     */
    private $calendar_api;

    /**
     * @var \local_pfc\api\evaluation_api
     */
    private $evaluation_api;

    /**
     * @var \local_pfc\api\evaluation_type_api
     */
    private $evaluation_type_api;

    /**
     * local_pfc_api_interface constructor.
     */
    public function __construct()
    {
        $api_client = new \local_pfc\api_client();
        $this->calendar_api = new \local_pfc\api\calendar_api($api_client);
        $this->evaluation_api = new \local_pfc\api\evaluation_api($api_client);
        $this->evaluation_type_api = new \local_pfc\api\evaluation_type_api($api_client);
    }
    /**
     * @return \local_pfc\models\calendar[]
     */
    function get_calendars() {
        return $this->calendar_api->get_calendars();
    }

    /**
     * @return \local_pfc\models\evaluation[]
     */
    function get_evaluations() {
        return $this->evaluation_api->get_evaluations();
    }

    /**
     * @return \local_pfc\models\evaluation_type[]
     */
    function get_evaluation_types() {
        return $this->evaluation_type_api->get_evaluation_types();
    }
}


class test_pfc_webservice extends advanced_testcase{


    public function test_get_calendars() {
        global $CFG;
        require_once($CFG->dirroot . '/local/pfc/lib.php');
        $WS= new local_pfc_webServiceConnector();
        $this->assertNotEmpty($WS->get_calendars());
    }


    public function test_get_evaluations() {
        global $CFG;
        require_once($CFG->dirroot . '/local/pfc/lib.php');
        $WS = new local_pfc_webServiceConnector();
        $this->assertNotEmpty($WS->get_evaluations());
    }

    public function test_get_evaluation_types() {
        global $CFG;
        require_once($CFG->dirroot . '/local/pfc/lib.php');
        $WS = new local_pfc_webServiceConnector();
        $this->assertNotEmpty($WS->get_evaluation_types());
    }


    public function test_webService_header(){

    }

}



class local_pfc_testcase extends advanced_testcase {
//    protected $testassertexecuted = false;
//
//    protected function setUp() {
//        parent::setUp();
//        if ($this->getName() === 'test_setup_assert') {
//            $this->assertTrue(true);
//            $this->testassertexecuted = true;
//            return;
//        }
//    }

    /**
     * Tests that bootstrapping has occurred correctly
     * @return void
     */
    public function test_bootstrap() {
        global $CFG;
        $this->assertTrue(isset($CFG->httpswwwroot));
        $this->assertEquals($CFG->httpswwwroot, $CFG->wwwroot);
        $this->assertEquals($CFG->prefix, $CFG->phpunit_prefix);
    }


//    public function test_webservice() {
//        $this->assertNotEmpty();
//    }

}


