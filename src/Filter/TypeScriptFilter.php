<?php

declare(strict_types = 1);

namespace WebLoader\Filter;

use WebLoader\Compiler;

/**
 * TypeScript filter
 *
 * @author Jan Tvrdík
 * @license MIT
 */
class TypeScriptFilter
{

	/** @var string|null */
	private $bin;

	/** @var array|null */
	private $env;


	public function __construct(string $bin = 'tsc', array $env = [])
	{
		$this->bin = $bin;
		$this->env = $env + $_ENV;
		unset($this->env['argv'], $this->env['argc']);
	}


	public function __invoke(string $code, Compiler $compiler, ?string $file = null): string
	{
		$file = (string) $file;

		if (pathinfo($file, PATHINFO_EXTENSION) === 'ts') {
			$out = substr_replace($file, 'js', -2);
			$cmd = sprintf('%s %s --target ES5 --out %s', $this->bin, escapeshellarg($file), escapeshellarg($out));
			Process::run($cmd, null, null, $this->env);
			$code = file_get_contents($out);
		}

		return (string) $code;
	}
}
