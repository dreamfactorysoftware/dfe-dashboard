#!/bin/sh
# DreamFactory Fabric(tm) Manager Bash/Zsh Autocomplete
# v1.0.1; gha; 201409251800;
#
# Put this script in your /etc/bash-completion.d/ directory or source it from your ~/.bashrc:
#
#   $ sudo cp bin/dreamfactory.bash-completion.sh /etc/bash_completion.d/
#
# or source (from project root):
#
#	$ . ./bin/dreamfactory.bash-completion.sh [command]
#
# To see the auto-completion in action, press the <TAB> key after entering the command name:
#
#	$ appname <TAB>
#

## Default to fabric, but use $1 as command name
COMMAND_NAME=${1:-sandman}

## zsh compatibility
if [[ -n ${ZSH_VERSION-} ]]; then
    autoload -U +X bashcompinit && bashcompinit
fi

## Variables
export COMP_WORDBREAKS="\ \"\\'><=;|&("

## Our auto-complete function
__complete_dreamfactory_console_app() {
    local current
    local cmds
    
    COMPREPLY=()
    current="${COMP_WORDS[COMP_CWORD]}"

    ##	Assume first word is the command
    command="${COMP_WORDS[0]}"

    ## Ensure that app is available...
    which ${command} > /dev/null || alias ${command} &> /dev/null || return

    if [[ ${COMP_CWORD} == 1 ]] ; then
        ##	No app name, just list commands
        cmds=`${command} --no-ansi | sed -n -e '/Available commands/,//p' | grep '^\s[\s]*' | awk '{ print $1 }'`
    else
        ##	Commands found, parse
        cmds=`${command} --no-ansi ${COMP_WORDS[1]} --help | sed -n -e '/Options/,/^$/p' | grep '^\s[\s]*' | awk '{ print $1 }'`
    fi

    COMPREPLY=( $(compgen -W "${cmds}" -- ${current}) )
    return 0
}

##
## Register the auto-complete handler
##

# If your application "executable" script is in a location other than
# the three below, add it to the end of the list. The Code Ninja recommends
# that you create a symbolic link, or shortcut for Windows, into a path in your
# system's $PATH environment for your commands:
#
# 	$ cd /usr/local/bin
#	$ sudo ln -s /path/to/dreamfactory/tool/bin/app
#	$ cd
#	$ app --help
#   $ app v1.2.3; DreamFactory Application > Help
#   $ blah blah blah
#

## Make sure this stays on one line. complete or bash doesn't like multiple lines...
complete -o nospace -F __complete_dreamfactory_console_app "${COMMAND_NAME}" "bin/${COMMAND_NAME}" "vendor/bin/${COMMAND_NAME}" "/usr/local/bin/${COMMAND_NAME}"
