<?php
namespace DreamFactory\Library\Console\Utility;

use DreamFactory\Library\Console\Enums\AnsiCodes;

/**
 * Cursor positioning utility
 */
class Cursor extends AnsiCodes
{
    /**
     * Moves the cursor
     *
     * $moves can be:
     *
     *  u           : move up
     *  d           : move down
     *  l           : move left
     *  r           : move right
     *  home        : move to top
     *  end         : move to bottom
     *  line_home   : move to line home
     *  line_end    : move to line end
     *
     * Multiple movements may be specified in a space-delimited list (i.e. "u d l r")
     *
     * @param   string $moves Where to move the cursor
     * @param   int    $count How many times to perform the sequence of moves
     *
     * @return string
     */
    public static function move( $moves, $count = 1 )
    {
        $_count = abs( $count ?: 1 );
        $_moves = explode( ' ', $moves );
        $_sequence = null;

        for ( $_i = 0; $_i < $_count; $_i++ )
        {
            foreach ( $_moves as $_move )
            {
                switch ( $_move )
                {
                    case 'u':   //  up
                        $_sequence .= static::render( static::CUU );
                        break;

                    case 'home':   //  home
                        $_sequence .= static::render( static::CUP, null, 1 );
                        break;

                    case 'd':   //  down
                        $_sequence .= static::render( static::CUD );
                        break;

                    case 'end':   //  bottom
                        $_sequence .= static::render( static::CUD, 0 );
                        break;

                    case 'r':   //  right
                        $_sequence .= static::render( static::CUF );
                        break;

                    case 'line_end':  //  line end
                        $_sequence .= static::render( static::CUF, 0 );
                        break;

                    case 'l':   //  left
                        $_sequence .= static::render( static::CUB, 1 );
                        break;

                    case 'line_home':  //  line start
                        $_sequence .= static::render( static::CHA, 1 );
                        break;
                }
            }
        }

        return $_sequence;
    }

    /**
     * Move to the line X and the column Y
     *
     * @param int $x
     * @param int $y
     *
     * @return string
     */
    public static function moveTo( $x = null, $y = null )
    {
        return static::render( static::CUP, $x, $y );
    }

    /**
     * Save current position.
     *
     * @param bool $attributes If true, save attributes and position
     *
     * @return string
     */
    public static function save( $attributes = false )
    {
        return static::render( $attributes ? static::SCPA : static::SCP );
    }

    /**
     * Restore cursor to the last saved position.
     *
     * @param bool $attributes If true, restore attributes and position
     *
     * @return string
     */
    public static function restore( $attributes = false )
    {
        return static::render( $attributes ? static::RCPA : static::RCP );
    }

    /**
     * Clear the screen
     *
     * $areas to clear can be:
     *
     *  all         : all and move cursor to 1, 1 (default)
     *  home        : from cursor to top of screen
     *  end         : from cursor to bottom of screen
     *  line_end    : from cursor to end of line
     *  line_home   : from cursor to start of line
     *
     * Multiple movements may be specified in a space-delimited list (i.e. "u d l r")
     *
     * @param   string $areas The areas of the screen to clear
     *
     * @return string
     */
    public static function clear( $areas = 'all' )
    {
        $_areas = explode( ' ', $areas );
        $_sequence = null;

        foreach ( $_areas as $_area )
        {
            switch ( $_area )
            {
                case 'all':
                    $_sequence .= static::render( static::ED2 );
                    break;

                case 'home':
                    $_sequence .= static::render( static::ED1 );
                    break;

                case 'end':
                    $_sequence .= static::render( static::ED0 );
                    break;

                case 'line_end':
                    $_sequence .= static::render( static::EL0 );
                    break;

                case 'line_home':
                    $_sequence .= static::render( static::EL1 );
                    break;

                case 'line':
                    $_sequence .= static::render( static::EL2 );
                    break;
            }
        }

        return $_sequence;
    }
}
