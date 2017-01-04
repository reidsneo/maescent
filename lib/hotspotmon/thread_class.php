<?php
class Threader
{
    var $threadName = null;
    var $rid = null;
    var $error = null;
    var $pipes = array();
    var $active = false;

  /**
   * Threader::Threader()
   *
   * The constructor which opens the process.
   *
   * @param mixed $cmd - Execute a shell command
   * @param mixed $vars - Pass arguments to shell command
   * @param string $name - Identifies your thread (useful for debug)
   * @return void
   */
    function Threader($cmd = null, $vars = null, $name = null)
    {
        $descriptorspec = array(0 => array("pipe", "r"), 1 => array("pipe", "w"),
            2 => array("pipe", "w"));
        $pipes = array();
        if (!empty($cmd)):
        $this->threadName = $name;
        try
        {
            $this->rid = proc_open("$cmd $vars", $descriptorspec, $this->
                pipes, null, $_ENV);
            $this->active = true;
        }
        catch (exception $e)
        {
            $this->active = false;
            $this->error = $e->getMessage();
        }
        endif;
    }

  /**
   * Threader::listen()
   *
   * While $this->active == true, you can monitor a thread by calling this method.
   *
   * @return string - Will return the output of the running process
   */
    public function listen()
    {
        if (is_resource($this->rid) && !empty($this->pipes))
        {
            $stdout = (isset($this->pipes['1'])) ? $this->pipes['1'] : null;
            return fgets($stdout);
        }
        else
        {
            return null;
        }
    }

    function __destruct()
    {
        $this->active = false;
        if (is_resource($this->rid))
        proc_close($this->rid);
        if (!empty($this->error))
        echo $this->error;
    }
}
?>