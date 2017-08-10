<?php
declare(strict_types = 1);
namespace Klapuch\Application;

/**
 * Body in XML format
 */
final class XmlBody implements Body {
	private $origin;

	public function __construct(Body $origin) {
	    $this->origin = $origin;
	}

	public function data(): string {
		$previous = libxml_use_internal_errors(true);
		try {
			$xml = simplexml_load_string($this->origin->data());
			if ($xml === false) {
				throw new \UnexpectedValueException(
					'XML document is not valid',
					0,
					new \Exception(
						implode(
							' | ',
							array_map(
								function(\LibXMLError $error): string {
									return trim($error->message);
								},
								libxml_get_errors()
							)
						)
					)
				);
			}
			return $xml->asXML();
		} finally {
			libxml_use_internal_errors($previous);
		}
	}
}