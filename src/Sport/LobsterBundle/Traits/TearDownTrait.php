<?php
/**
 * TearDownTrait.php
 *
 * User: mikey
 * Date: 26/03/2014
 * Time: 17:13
 */

namespace  Sport\LobsterBundle\Traits;

use \Mockery as m;

/**
 * Class TearDownTrait
 *
 * Just use this trait rather than having to implement your own tearDown that closes Mockery
 *
 * @package Sport\LobsterBundle\Traits
 */
trait TearDownTrait
{
    public function tearDown()
    {
        m::close();
    }
}
