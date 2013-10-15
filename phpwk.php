<?php
/**  
 * File: phpMk Version: 0.1
 * Author: Michael San Martim <michaelsanmartim@gmail.com>
 * Created: Seg 21 Jan 2013 11:34:26 BRST
 * Last Update: Seg 21 Jan 2013 11:34:26 BRST
 * Notes: class php mkhtmltopdf
 *
 * put in your css to not break tables
 * table{page-break-inside: avoid;}
 * table { display:table-row-group; display: table-row; display:table-cell;}
 * 
 * http://madalgo.au.dk/~jakobt/wkhtmltoxdoc/wkhtmltopdf_0.10.0_rc2-doc.html
 */


class phpwk
{
	private $options;
	private $html;
	private $time;
	private $tmp;
	private $bin;
	private $clean = true;

	function phpwk($format = 'A4', $orientation='Portrait', $marginTop = '10', $marginRight = '10', $marginBottom = '10', $marginLeft = '10')
	{
		$this->options['page-size'] = $format;
		$this->options['orientation'] = $orientation;
		$this->options['margin-top'] = $marginTop;
		$this->options['margin-right'] = $marginRight;
		$this->options['margin-bottom'] = $marginBottom;
		$this->options['margin-left'] = $marginLeft;
		$this->options['dpi'] = 90;
		$this->options['encoding'] = 'utf-8';
		$this->time = microtime(true);
		$this->tmp = dirname(realpath( __FILE__ )).'/tmp/';
		$this->bin = dirname(realpath( __FILE__ )).'/bin/';
	}

	function setOptions($options = array())
	{
		$this->options = array_merge($this->options, $options);
	}

	function setHtml($html)
	{
		$this->html = is_file($html) ? $html : $this->createTmpFile($html);
	}

	function setCss($css)
	{
		$this->options['user-style-sheet'] = is_file($css) ? $css : $this->createTmpFile($css, 'css');
	}

	function setHeader($header)
	{
		$this->options['header-html'] = is_file($header) ? $header : $this->createTmpFile($header, 'header.html');
	}

	function setFooter($foo)
	{
		$this->options['footer-html'] = is_file($foo) ? $foo : $this->createTmpFile($foo, 'footer.html');
	}

	function createTmpFile($file, $type = 'html')
	{
		if (mb_detect_encoding($file) == 'UTF-8')
		{
			//$file = utf8_decode($file);	
		}
		$name = $this->tmp.$this->time.".".$type;
		file_put_contents($name, $file);
		return $name;
	}

	function getOptions()
	{
		array_walk($this->options, function($val,$key) use(&$options){ 
			$options .= '--'.$key.' '.$val.' ';
		});
		return $options;
	}

	function output($file = null)
	{
		if ($file == null)
		{
			$file = $this->tmp.$this->time.'.pdf';
		}
		$command = $this->getOptions();
		$descriptorspec = array(
			2 => array("file", $this->tmp."error-output.txt", "a"));
		$command .= $this->html.' '.$file;

		if ($this->clean)
		{
			$command .= ';'.$this->clean();
		}

		$process = proc_open($this->bin."wkhtmltopdf-amd64 $command", $descriptorspec, $pipes, $cwd);
	}

	function clean()
	{
		$clean = 'rm -f '.$this->options['user-style-sheet'].";";	
		$clean .= 'rm -f '.$this->options['header-html'].';';	
		$clean .= 'rm -f '.$this->options['footer-html'].';';	
		$clean .= 'rm -f '.$this->html.';';

		return $clean;
	}
	
}
?>
