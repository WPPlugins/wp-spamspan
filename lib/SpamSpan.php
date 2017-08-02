<?php

/**
 * Munge text to add "spamspan" protection to email addresses.
 *
 * The spamspan.js script <http://www.spamspan.com/> can process
 * the munged out to restore the original email addresses as
 * clickable links.
 */
class SpamSpan {

	/*
	 * The definitions below correspond to values in distributed "spamspan.js".
	 */

	/** The "@" in the email address is replaced with this string. */
	public static $at_replacement = " [at] ";

	/** All the "." on the right side of the email address are replaced with this string. */
	public static $dot_replacement = " [dot] ";

	/** The class value for the <span class="..."> enclosing the entire email address in the result. */
	public static $address_class = "spamspan";

	/** The class value for the <span class="..."> enclosing the left side of the email address in the result. */
	public static $user_class = "u";

	/** The class value for the <span class="..."> enclosing the right side of the email address in the result. */
	public static $domain_class = "d";


	/**
	 * Rewrite an email address with "spamspan" protection.
	 *
	 * @param $addr - The email address to protect.
	 * @returns The email address munged with "spamspan" protection.
	 */
	public static function protect($addr) {
		$at = strpos($addr, '@');
		if ($at === FALSE) {
			return $addr;
		}
		$lhs = substr($addr, 0, $at);
		$rhs = str_replace('.', self::$dot_replacement, substr($addr, $at+1));
		return '<span class="' . self::$address_class  . '">'
			. '<span class="' . self::$user_class  . '">' . $lhs . '</span>'
			. self::$at_replacement
			. '<span class="' . self::$domain_class  . '">' . $rhs . '</span>'
			.  '</span>';
	}


	/**
	 * Apply "spamspan" protection to the entire body of plain text.
	 *
	 * @param $text - The text content to protect.
	 * @returns The text content munged with "spamspan" protection.
	 *
	 * Uses a regexp to pick out email addresses from body of text.
	 *
	 * regexp from: http://www.regular-expressions.info/email.html
	 */
	public static function filter_text($text) {
		$f = create_function('$a', 'return SpamSpan::protect($a[0]);');
		return preg_replace_callback('/\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b/i', $f, $text);
	}


	/**
	 * Apply "spamspan" protection to html content.
	 *
	 * @param $html - The html content to protect.
	 * @returns The html content munged with "spamspan" protection.
	 *
	 * Text within <angle_brackets> will be preserved.
	 *
	 * TODO: Add support for [noss] ... [/noss] quoting.
	 */
	public static function filter_html($html) {

		$len = strlen($html);
		$result = '';
		$offset = 0;
		while ($offset < $len) {

			$lpos = strpos($html, '<', $offset);

			# If no "<" found then no tags left. Filter rest of text and finish.
			if ($lpos === FALSE) {
				$result .= SpamSpan::filter_text(substr($html, $offset));
				break;
			}

			# Apply spamspan filter to text from $offset up to "<".
			if ($lpos > 0) {
				$result .= SpamSpan::filter_text(substr($html, $offset, $lpos-$offset));
			}

			$rpos = strpos($html, '>', $lpos);
			
			# Ooops! No close ">" found to this tag.
			if ($rpos === FALSE) {
				$result .= substr($html, $lpos);
				break;
			}

			# Copy over the tag without processing.
			$offset = $rpos + 1;
			$result .= substr($html, $lpos, $offset - $lpos);

		}

		return $result;
	}


}

