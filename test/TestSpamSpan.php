<?php

require_once(__DIR__ . "/../lib/SpamSpan.php");

class TestSpamSpan extends PHPUnit_Framework_TestCase {

	var $testdata = array(
		"fred@example.com" => '<span class="spamspan"><span class="u">fred</span> [at] <span class="d">example [dot] com</span></span>',
		"fred+tag@example.com" => '<span class="spamspan"><span class="u">fred+tag</span> [at] <span class="d">example [dot] com</span></span>',
		"moe.howard@example.com" => '<span class="spamspan"><span class="u">moe.howard</span> [at] <span class="d">example [dot] com</span></span>'
	);


	public function testProtect() {
		foreach($this->testdata as $addr => $expect) {
			$this->assertEquals($expect, SpamSpan::protect($addr));
		}
	}

	public function testFilterText() {
		$textbits = array(
			"The quick brown fox jumped over the lazy dog.",
			"Now is the time for all good men to come to the aid of their party.",
			"A stitch in time saves nine.",
			"Four score and seven years ago our fathers brought forth on this continent a new nation."
		);
		$keys = array_keys($this->testdata);
		$values = array_values($this->testdata);

		$text = array($textbits[0]);
		$expect = array($textbits[0]);
		for ($i = 0 ; $i < count($this->testdata) ; ++$i) {
			array_push($text, $keys[$i], $textbits[$i+1]);
			array_push($expect, $values[$i], $textbits[$i+1]);
		}
		$text = join($text, " ");
		$expect = join($expect, " ");
		$this->assertEquals($expect, SpamSpan::filter_text($text));
	}

	public function testFilterHTML() {
		$htmlbits = array(
			"The <b>quick brown fox</b> jumped over the lazy dog.",
			"Now <a href=\"mailto:alice@example.com\">is the time</a> for all good men to come to the aid of their party.<pre>",
			"</pre>A stitch in time <input type=\"text\" value=\"bob@example.com\" /> saves nine.",
			"<p>Four score and seven years ago our fathers brought forth on this continent a new nation.</p>"
		);
		$keys = array_keys($this->testdata);
		$values = array_values($this->testdata);

		$html = array($htmlbits[0]);
		$expect = array($htmlbits[0]);
		for ($i = 0 ; $i < count($this->testdata) ; ++$i) {
			array_push($html, $keys[$i], $htmlbits[$i+1]);
			array_push($expect, $values[$i], $htmlbits[$i+1]);
		}
		$html = "<div>" . join($html, " ") . "</div>";
		$expect = "<div>" . join($expect, " ") . "</div>";
		$this->assertEquals($expect, SpamSpan::filter_html($html));
	}

	public function testFilterHTML_malformed() {
		$h = "Malformed <input type=\"text\" value=\"bob@example.com\"";
		$this->assertEquals($h, SpamSpan::filter_html($h));
	}

}

