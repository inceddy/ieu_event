<?php

/*
 * This file is part of ieUtilities.
 *
 * (c) 2015 Philipp Steingrebe <philipp@steingrebe.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ieu\Traits;

/**
 * Provides methods for hook handling
 * 
 * @author Philipp Steingrebe <philipp@steingrebe.de>
 */

trait Hooks
{
	/**
	 * Stapel mit allen Callback-Funktionen
	 * @var array
	 */
	protected $hooks = array();

	/**
	 * Interner Hookzähler
	 * @var integer
	 */
	protected $hookPosition = 0;


	/**
	 * Setzt einen neuen Hook auf dieses Objekt
	 *
	 * @param string  $name       der Name(Namespace) des Hooks
	 * @param mixed   $callback   ein Callback der Form callable oder array(class/object, method)
	 * @param integer $postition  die Position in der Hookreihenfolge (optional)
	 * 
	 * @return object self
	 * 
	 */

	public function addHook($name, $callback, $position = null)
	{
		list($name, $namespace) = array_pad(explode('.', $name, 2), 2, null);


		if (!isset($this->hooks[$name])) {
			$this->hooks[$name] = array();
		}

		if ($position === null) {
			$position = isset($this->hooks[$name][$this->hookPosition + 1]) ? $this->hookPosition += 2 : ++$this->hookPosition;
		}

		$this->hooks[$name][$position] = array($namespace, $callback);

		return $this;
	}

	
	/**
	 * Entfernt Hooks anhand eines Names und den daran
	 * angeschlossenen Namespace von diesem Object.
	 *
	 * Namespace Bsp.: change.myext.core wobei change der Name und
	 * mytext.core der Namespace ist
	 *
	 * @param string $name  der Name und Namespace der Hooks
	 * 
	 * @return object self
	 * 
	 */

	public function removeHook($name)
	{
		list($name, $namespace) = array_pad(explode('.', $name, 2), 2, null);
		
		foreach ($this->hooks[$name] as $index => $hook) {
			if ($namespace === null || strpos($hook[0], $namespace) === 0) {
				unset($this->hooks[$name][$index]);
			}
		}

		return $this;
	}


	/**
	 * Löst einen Hook per Name bzw. Namespace aus. Das Argument wird dabei
	 * nacheinander an jeden Callback in diesem Namespace gereicht und 
	 * das Ergebnis zurück gegeben. 
	 *
	 * @param string $name      der Name und Namespace der Hooks
	 * @param mixed  $argument  das Argument dieses Hooks
	 * 
	 * @return mixed
	 * 
	 */

	public function fireHook($name, $argument)
	{
		list($name, $namespace) = array_pad(explode('.', $name, 2), 2, null);

		// Direkt zurückgeben, wenn keine Hook vorliegt
		if (!isset($this->hooks[$name])) {
			return $argument;
		}

		foreach ($this->hooks[$name] as $hook) {
			if ($namespace === null || strpos($hook[0], $namespace) === 0) {
				$argument = call_user_func($hook[1], $argument);
			}

			if (is_null($argument)) {
				throw new \Exception('missing_response');
			}
		}

		return $argument;
	}
}