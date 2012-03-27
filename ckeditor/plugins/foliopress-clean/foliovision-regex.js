/**
 * foliovision-regex is custom regular expression interpreter that can compile text into
 * regular expression string (ECMA definition). This text has some simple commands explained below.
 *
 * @version		0.1
 * @package		Foliovision.Basic
 * @author		Peter Baran 
 */


FV_Regex = {

	/**  
	 * Set true if you want to @see VerifyString to verify also '()'
	 * @type		bool
	 */
	bUseParentheses : true,
	/**  
	 * Set true if you want to @see VerifyString to verify also '[]'
	 * @type		bool
	 */
	bUseBrackets : true,
	/**  
	 * Set true if you want to @see VerifyString to verify also '{}'
	 * @type		bool
	 */
	bUseBraces : false,

	/**
	 * This function verifies if some string that should be parsed as Regular expression is well bracketed.
	 *
	 * @param string strCode		text to be verified
	 * @return bool		True if expression is well bracketed, false otherwise 
	 */
	VerifyString : function( strCode ){
		var iParentheses = 0;
		var iBrackets = 0;
		var iBraces = 0;
		var aExpected = new Array();
	
		for( var i=0; i<strCode.length; i++ ){
			var chChar = strCode.charAt( i );
			
			if( '(' == chChar && this.bUseParentheses ){
				iParentheses++;
				aExpected.push( ')' );
			}
			if( '[' == chChar && this.bUseBrackets ){
				iBrackets++;
				aExpected.push( ']' );
			}
			if( '{' == chChar && this.bUseBraces ){
				iBraces++;
				aExpected.push( '}' );
			}
			
			if( (this.bUseParentheses && ')' == chChar) && chChar == aExpected.pop() ) iParentheses--;
			else if( this.bUseParentheses && ')' == chChar ) return false;
			if( (this.bUseBrackets && ']' == chChar) && chChar == aExpected.pop() ) iBrackets--;
			else if( this.bUseBrackets && ']' == chChar ) return false;
			if( (this.bUseBraces && '}' == chChar) && chChar == aExpected.pop() ) iBraces--;
			else if( this.bUseBraces && '}' == chChar ) return false;
			
			if( this.bUseParentheses && iParentheses < 0 ) return false;
			if( this.bUseBrackets && iBrackets < 0 ) return false;
			if( this.bUseBraces && iBraces < 0 ) return false;
		}
		if( iParentheses + iBrackets + iBraces != 0 ) return false;
		
		return true;
	},
	
	/**
	 * Converts text into regular expression text (ECMA definition).
	 * Special characters:
	 *    (): used for grouping, use '\(', '\)' to check for '(', ')'
	 *    []: used to definy one of some characters inside brackets, use '\[', '\]' to check for '[', ']'
	 *    \+: 1 or more characters previously specified
	 *    \*: 0 or more characters previously specified
	 *    \w: any alpha-numeric character
	 *    \\: backslash
	 *
	 * @param string strCode		text to be converted to regular expression
	 * @return string		string of regular expression (ECMA definition)
	 */
	ConvertString : function( strCode ){
		var strRegex = '';
		var bSlash = false;
	
		for( var i=0; i<strCode.length; i++ ){
			var iChar = strCode.charCodeAt( i );
			var chChar = strCode.charAt( i );
		
			if( bSlash ){
				if( 'w' == chChar ) strRegex += "\\w";
				if( '*' == chChar ) strRegex += '*';
				if( '+' == chChar ) strRegex += '+';
				if( '?' == chChar ) strRegex += '?';
				if( '.' == chChar ) strRegex += '.';
				if( '[' == chChar ) strRegex += '\\x5B';
				if( ']' == chChar ) strRegex += '\\x5D';
				if( '(' == chChar ) strRegex += '\\x28';
				if( ')' == chChar ) strRegex += '\\x29';
				if( '\\' == chChar ) strRegex += '\\x5C';
			
				bSlash = false;
			}else{
				if( iChar >= 48 && iChar <= 57 ) strRegex += String( iChar ); // 0-9
				else if( iChar >= 65 && iChar <= 90 ) strRegex += chChar; // A-Z
				else if( iChar >= 97 && iChar <= 122 ) strRegex += chChar; // a-z
				else if( '\\' == chChar ) bSlash = true; // start of special character
				else if( ' ' == chChar ) strRegex += '\\s'; // empty space
				else if( '(' == chChar ) strRegex += '('; // start of enclosed section
				else if( ')' == chChar || '[' == chChar || ']' == chChar ) strRegex += chChar; // special characters that are copied
				else strRegex += '\\x' + Number( iChar ).toString( 16 ).toUpperCase(); // other characters are transformed into its ASCII code
			}
		}
	
		return strRegex;
	}
}