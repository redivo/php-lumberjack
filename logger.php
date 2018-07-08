<?php

namespace LumberjackLogger;

/**************************************************************************************************/

define('LUMBERJACK_ERROR',   0);  //!< Things that went wrong
define('LUMBERJACK_WARNING', 1);  //!< Warning about soft issues
define('LUMBERJACK_INFO',    2);  //!< Informations
define('LUMBERJACK_TRACE',   3);  //!< Trace the flow
define('LUMBERJACK_DEBUG',   4);  //!< Very verbose debug

/**************************************************************************************************/

define('LOGGER_CONFIG_FILE', 'lumberjack.ini'); //!< Configuration file location

/**************************************************************************************************/
/**
 * \brief  Inform if a given severity is a user valid severity
 * \param  $severity  Severity to be evaluated
 * \return TRUE if the severity is valid, FALSE otherwise
 */
function isSeverityValid($severity) {
    switch ($severity) {
        case LUMBERJACK_ERROR:
        case LUMBERJACK_WARNING:
        case LUMBERJACK_INFO:
        case LUMBERJACK_TRACE:
        case LUMBERJACK_DEBUG:
            return TRUE;

        default:
            return FALSE;
    }
}

/**************************************************************************************************/
/**
 * \brief  Stringify a user severity
 * \param  $severity  Severity to be stringfied
 * \return The stringified name of severity or UNKNOWN if it's not in the list
 */
function stringifySeverity($severity)
{
    switch($severity) {
        case LUMBERJACK_ERROR:
            return 'ERROR';

        case LUMBERJACK_WARNING:
            return 'WARNING';

        case LUMBERJACK_INFO:
            return 'INFO';

        case LUMBERJACK_TRACE:
            return 'TRACE';

        case LUMBERJACK_DEBUG:
            return 'DEBUG';

        default:
            return 'UNKNOWN';
    }
}

/**************************************************************************************************/
/**
 * \brief  Translate config severity to user severity value
 * \param  $severityCfg  Severity to be translated
 * \return Translated severity
 */
function logTranslateServerityConfig($severityCfg)
{
    switch($severityCfg) {
        case "ERROR":
            return LUMBERJACK_ERROR;

        case "WARNING":
            return LUMBERJACK_WARNING;

        case "INFO":
            return LUMBERJACK_INFO;

        case "TRACE":
            return LUMBERJACK_TRACE;

        case "DEBUG":
            return LUMBERJACK_DEBUG;

        case "NONE":
        default:
            return -1;
    }
}

/**************************************************************************************************/
/**
 * \brief  Log a message
 * \param  $severity  Severity of log
 * \param  $message   Message to be logged
 * \return TRUE on success. FALSE otherwise
 */
function logMessage($severity, $message)
{
    $cfg = parse_ini_file(LOGGER_CONFIG_FILE);

    // If the config file cannot be read, just exit
    if ($cfg == FALSE) {
        return FALSE;
    }

    // Sanitize given severity
    if (!isSeverityValid($severity)) {
        return FALSE;
    }

    // If configured severity is not set, just create it with ERROR value
    if (empty($cfg['severity'])) {
        $cfg['severity'] = 'ERROR';
    }


    // If the configurated severity is less than request severity, doesn't log
    if (logTranslateServerityConfig($cfg['severity']) < $severity) {
        return TRUE;
    }

   return error_log(PHP_EOL
                        . '[' . date("Y-m-d h:m:s") . '] '
                        . '[' . stringifySeverity($severity) . '] '
                        . '[' . debug_backtrace()[0]['file']
                                . ' : ' . debug_backtrace()[0]['line'] . '] '
                        . $message
                        . PHP_EOL,
                    3,
                    $cfg['log_file']);
}

/**************************************************************************************************/

?>
