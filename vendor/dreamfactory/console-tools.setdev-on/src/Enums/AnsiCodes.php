<?php
/**
 * This file is part of the DreamFactory Console Tools Library
 *
 * Copyright 2014 DreamFactory Software, Inc.
 * <support@dreamfactory.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
namespace DreamFactory\Library\Console\Enums;

use DreamFactory\Library\Utility\Enums\FactoryEnum;

/**
 * Enumerations of ANSI escape-code sequences. Coordinates are not zero-based.
 * That is to say the first column and row is 1.
 *
 * @author    Jerry Ablan <jerryablan@dreamfactory.com>
 */
class AnsiCodes extends FactoryEnum
{
    //******************************************************************************
    //* Device Status
    //******************************************************************************

    /**
     * @type string
     */
    const QUERY_CODE = "\e[c";
    /**
     * @type string
     */
    const QUERY_CODE_REPORT = "\e[#0c";
    /**
     * @type string
     */
    const QUERY_STATUS = "\e[5n";
    /**
     * @type string
     */
    const STATUS_OK = "\e[0n";
    /**
     * @type string
     */
    const STATUS_FAIL = "\e[3n";
    /**
     * @type string
     */
    const QUERY_POSITION = "\e[6n";
    /**
     * @type string
     */
    const QUERY_POSITION_REPORT = "\e[#;#R";

    //******************************************************************************
    //* Terminal
    //******************************************************************************

    /**
     * @type string Clears the entire screen, cursor left at 1,1
     */
    const CLS = "\e[1;1H;\e[0J;";
    /**
     * @type string Reset the terminal to default settings
     */
    const RESET = "\ec";
    /**
     * @type string Enable line wrapping
     */
    const WRAP_ON = "\e[7h";
    /**
     * @type string Disable line wrapping
     */
    const WRAP_OFF = "\e[7l";
    /**
     * @type string Disable line wrapping
     */
    const DEFAULT_FONT = "\e(";
    /**
     * @type string Disable line wrapping
     */
    const ALT_FONT = "\e)";

    //******************************************************************************
    //* Cursor Movement Constants
    //******************************************************************************

    /**
     * @type string Cursor Up: Moves the cursor up by the specified number of lines without changing columns. If the
     *       cursor is already on the top line, this sequence is ignored. \e[A is equivalent to \e[1A.
     */
    const CUU = "\e[#A";
    /**
     * @type string Cursor Down: Moves the cursor down by the specified number of lines without changing columns. If
     *       the cursor is already on the bottom line, this sequence is ignored. \e[B is equivalent to \e[1B.
     */
    const CUD = "\e[#B";
    /**
     * @type string Cursor Forward: Moves the cursor forward by the specified number of columns without changing lines.
     *       If the cursor is already in the rightmost column, this sequence is ignored. \e[C is equivalent to \e[1C.
     */
    const CUF = "\e[#C";
    /**
     * @type string Cursor Backward: Moves the cursor back by the specified number of columns without changing lines.
     *       If the cursor is already in the leftmost column, this sequence is ignored. \e[D is equivalent to \e[1D.
     */
    const CUB = "\e[#D";
    /**
     * @type string Cursor Next Line: Moves the cursor down the indicated # of rows, to column 1. \e[E is equivalent to
     *       \e[1E.
     */
    const CNL = "\e[#E";
    /**
     * @type string Cursor Preceding Line: Moves the cursor up the indicated # of rows, to column 1. \e[F is equivalent
     *       to \e[1F.
     */
    const CPL = "\e[#F";
    /**
     * @type string Cursor Horizontal Absolute: Moves the cursor to indicated column in current row. \e[G is equivalent
     *       to \e[1G.
     */
    const CHA = "\e[#G";
    /**
     * @type string Cursor Position: Moves the cursor to the specified position. The first # specifies the line number,
     *       the second # specifies the column. If you do not specify a position, the cursor moves to the home
     *       position: the upper-left corner of the screen (line 1, column 1).
     */
    const CUP = "\e[#;#H";
    /**
     * @type string Force Horizontal and Vertical Position. Works in the same way as the preceding escape sequence.
     */
    const HVP = "\e[#;#f";
    /**
     * @type string Save Cursor Position: Saves the current cursor position. You can move the cursor to the saved
     *       cursor position by using the Restore Cursor Position sequence.
     */
    const SCP = "\e[s";
    /**
     * @type string Restore/Unsave Cursor Position: Returns the cursor to the position stored by the Save Cursor
     *       Position sequence.
     */
    const RCP = "\e[u";
    /**
     * @type string Save Cursor Position and Attributes: Saves the current cursor position. You can move the cursor to
     *       the saved cursor position by using the Restore Cursor Position sequence.
     */
    const SCPA = "\e7";
    /**
     * @type string Restore/Unsave Cursor Position and Attributes: Returns the cursor to the position stored by the
     *       Save Cursor Position sequence.
     */
    const RCPA = "\e8";

    //******************************************************************************
    //* ED (Erase Display) Constants
    //******************************************************************************

    /**
     * @type string Clears the screen from cursor to end of display. The cursor position is unchanged.
     */
    const ED = "\e[#J";
    /**
     * @type string Clears the screen from cursor to end of display. The cursor position is unchanged.
     */
    const ED0 = "\e[0J";
    /**
     * @type string Clears the screen from start to cursor. The cursor position is unchanged.
     */
    const ED1 = "\e[1J";
    /**
     * @type string Clears the screen and moves the cursor to the home position ( line 1, column 1 ).
     */
    const ED2 = "\e[2J";

    //******************************************************************************
    //* EL (Erase Line) Constants
    //******************************************************************************

    /**
     * @type string Clears all characters from the cursor position to the end of the line ( including the character at
     *       the cursor position ). The cursor position is unchanged.
     */
    const EL = "\e[#K";
    /**
     * @type string Clears all characters from the cursor position to the end of the line ( including the character at
     *       the cursor position ). The cursor position is unchanged.
     */
    const EL0 = "\e[0K";
    /**
     * @type string Clears all characters from start of line to the cursor position. ( including the character at the
     *       cursor position ). The cursor position is unchanged.
     */
    const EL1 = "\e[1K";
    /**
     * @type string Clears all characters of the whole line. The cursor position is unchanged.
     */
    const EL2 = "\e[2K";

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @param string $code
     * @param int    $value1
     * @param int    $value2
     *
     * @return mixed
     */
    public static function render( $code, $value1 = null, $value2 = null )
    {
        if ( false !== ( $_pos = strpos( $code, '#' ) ) )
        {
            $code = substr( $code, 0, $_pos - 1 ) .
                ( null === $value1 ? null : ( ( $value1 > 1 ? 9999 : $value1 ) ?: 1 ) ) .
                substr( $code, $_pos + 1 );
        }

        if ( false !== ( $_pos = strpos( $code, '#' ) ) )
        {
            $code = substr( $code, 0, $_pos - 1 ) .
                ( null === $value2 ? null : ( ( $value2 > 1 ? 9999 : $value2 ) ?: 1 ) ) .
                substr( $code, $_pos + 1 );
        }

        //  If all the pound signs aren't replaced, throw an error
        if ( false !== strpos( $code, '#' ) )
        {
            throw new \InvalidArgumentException( 'The code provided still contains an empty placeholder: ' . $code );
        }

        return $code;
    }
}
