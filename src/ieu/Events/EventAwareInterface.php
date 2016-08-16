<?php

/*
 * This file is part of ieUtilities.
 *
 * (c) 2016 Philipp Steingrebe <philipp@steingrebe.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ieu\Events;

interface EventAwareInterface {

	public function addEvent($name, callable $callback);

	public function removeEvent($name);

	public function fireEvent(EventInterface $event);

}
