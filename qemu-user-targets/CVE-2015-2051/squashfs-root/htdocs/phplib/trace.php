<?

function TRACE_debug($message)
{
	$debug = query("/debug/php");
	if($debug!=0)
	{
		fwrite("w", "/dev/console", $message."\n");
	}
}

function TRACE_info($message)
{
	fwrite("w", "/dev/console", $message."\n");
}

function TRACE_error($message)
{
	fwrite("w", "/dev/console", $message."\n");
}

function SHELL_debug($shell, $message)
{
	fwrite("a", $shell, "echo \"".$message."\"\n");
}

function SHELL_info($shell, $message)
{
	fwrite("a", $shell, "echo \"".$message."\"\n");
}

function SHELL_error($shell, $message)
{
	fwrite("a", $shell, "echo \"".$message."\"\n");
}
?>
