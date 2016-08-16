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

class Event implements EventInterface {

	private $eventName;

	private $eventNamespace;

	private $eventTarget;

	private $eventRelatedTarget;


	public function __construct($name, $relatedTarget = null) {
		list($this->eventName, $this->eventNamespace) = self::getEventNameAndNamespace($name);
		$this->eventRelatedTarget = $relatedTarget;
	}

	public function getEventName()
	{
		return $this->eventName;
	}

	public function getEventNamespace()
	{
		return $this->eventNamespace;
	}

	public function setEventTarget(EventsAwareInterface $target)
	{
		$this->eventTarget = $target;
		return $this;
	}

	public function getEventTarget()
	{
		return $this->eventTarget;
	}

	public function getEventRelatedTarget()
	{
		return $this->eventRelatedTarget;
	}

    /**
     * Trennt den Namen und den Namespace.
     *
     * @param  string $name  name.namespace
     *
     * @return array
     * 
     */
    
    private static function getEventNameAndNamespace($name)
    {
    	return array_pad(explode('.', $name, 2), 2, null);
    }
}