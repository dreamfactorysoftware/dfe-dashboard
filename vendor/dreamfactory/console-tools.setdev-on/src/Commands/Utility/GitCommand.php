<?php
namespace DreamFactory\Library\Console\Commands\Utility;

use DreamFactory\Library\Console\Commands\BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Git utilities
 */
class GitCommand extends BaseCommand
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @param string $name
     * @param array  $config
     */
    public function __construct( $name = 'util:git', array $config = array() )
    {
        $_config = array(
            'description' => 'Runs various git commands',
            'definition'  => array(
                new InputArgument(
                    'operation',
                    InputArgument::REQUIRED,
                    'The git operation to run',
                    null
                ),
                new InputOption( 'from', 'f', InputOption::VALUE_OPTIONAL, 'The optional source remote' ),
                new InputOption( 'to', 't', InputOption::VALUE_OPTIONAL, 'The target remote' ),
                new InputOption( 'update', 'u', InputOption::VALUE_NONE, 'If used, the new remote will be updated' ),
            ),
            'help'        => <<<EOT
The <info>util:git</info> command performs a few git utility functions.

<info>sandman util:git <comment>operation</comment> [[--from|-f][--to|-t]]</info>

<comment>operation</comment> can be one of the following:

    <info>replace-remote</info>

EOT
        );

        parent::__construct( $name, array_merge( $_config, $config ) );
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     * @throws \Exception
     */
    protected function execute( InputInterface $input, OutputInterface $output )
    {
        $_from = $_to = $_update = false;

        //  Let's go!
        $this->writeInPlace( ' - initializing...' );

        switch ( $input->getArgument( 'operation' ) )
        {
            case 'replace-remote':
                try
                {
                    $_from = $input->getOption( 'from' );
                    $_to = $input->getOption( 'to' );
                }
                catch ( \Exception $_ex )
                {
                    $_from = $_to = false;
                }

                if ( empty( $_from ) || empty( $_to ) )
                {
                    throw new \InvalidArgumentException(
                        'Both <comment>--from</comment> and <comment>--to</comment> must be specified when using the "replace-remote" command.'
                    );
                }

                $_path = getcwd();
                $this->writeln( ' - Repository path is <comment>' . $_path . '</comment>' );
                $this->writeInPlace( '   - Replacing remote <comment>' . $_from . '</comment> with <comment>' . $_to . '</comment>' );
                $this->_replaceRemote( $_path, $_from, $_to, $input->getOption( 'update' ) );
                break;
        }

        $this->writeln( ' - completed replacing remote.' );

        return 0;
    }

    /**
     * @param string $path   The path to the repository to operation upon
     * @param string $from   The remote to replace
     * @param string $to     The replacement remote
     * @param bool   $update If true, 'git remote update' will be run after the replacement is made
     *
     * @return bool
     */
    protected function _replaceRemote( $path, $from, $to, $update = false )
    {
        $_path = ( $path ?: getcwd() ) . DIRECTORY_SEPARATOR . '.git';

        if ( !is_dir( $_path ) )
        {
            throw new \InvalidArgumentException( 'The path "' . $path . '" is not a git repository.' );
        }

        $_filename = $_path . DIRECTORY_SEPARATOR . 'config';

        if ( false === ( $_config = file_get_contents( $_filename ) ) )
        {
            throw new \RuntimeException( 'The git config file could not be read.' );
        }

        $this->writeln( '   - Config file "' . $_filename . '" read.' );

        if ( false !== stripos( $_config, $from ) )
        {
            $_config = str_ireplace( $from, $to, $_config );

            if ( false === file_put_contents( $_filename, $_config ) )
            {
                throw new \RuntimeException( 'The remote was replaced, but the config file could not be written to disk.' );
            }

            $this->writeln( '   - Remote <comment>' . $from . '</comment> replaced' );

            return true;
        }

        $this->writeln( '   - Remote <comment>' . $from . '</comment> not found.' );

        return false;
    }
}

