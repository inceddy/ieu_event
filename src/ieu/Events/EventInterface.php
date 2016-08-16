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

interface EventInterface {

	/**
	 * Gibt den Namen des Events zurück
	 *
	 * @return string  der Name des Events
	 * 
	 */
	
	public function getEventName();

	/**
	 * Gibt den Namensraum des Events zurück
	 *
	 * @return string der Namensraum des Events
	 */
	
	public function getEventNamespace();

	public function setEventTarget(EventsInterface $target);

	public function getEventTarget();

	public function getEventRelatedTarget();
}
