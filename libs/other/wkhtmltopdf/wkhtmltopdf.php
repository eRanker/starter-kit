<?php

// Automated configuration; Modify these if they fail. (they shouldn't ;) )
$GLOBALS['WKPDF_BASE_PATH'] = dirname(str_replace('\\', '/', __FILE__)) . '/';
if (defined("WP_HOME")) {
    $GLOBALS['WKPDF_BASE_SITE'] = trim(WP_HOME, ' /') . "/";
} else {
    $GLOBALS['WKPDF_BASE_SITE'] = trim('http://' . $_SERVER['HTTP_HOST'], ' /') . "/";
}

/**
 * @author Christian Sciberras
 * @see <a href="http://code.google.com/p/wkhtmltopdf/">http://code.google.com/p/wkhtmltopdf/</a>
 * @copyright 2010 Christian Sciberras / Covac Software.
 * @license LGPL
 * @example
 *   <font color="#008800"><i>//-- Create sample PDF and embed in browser. --//</i></font><br>
 *   <br>
 *   <font color="#008800"><i>// Include WKPDF class.</i></font><br>
 *   <font color="#0000FF">require_once</font>(<font color="#FF0000">'wkhtmltopdf/wkhtmltopdf.php'</font>);<br>
 *   <font color="#008800"><i>// Create PDF object.</i></font><br>
 *   <font color="#EE00EE">$pdf</font>=new <b>WKPDF</b>();<br>
 *   <font color="#008800"><i>// Set PDF's HTML</i></font><br>
 *   <font color="#EE00EE">$pdf</font>-><font color="#0000FF">set_html</font>(<font color="#FF0000">'Hello &lt;b&gt;Mars&lt;/b&gt;!'</font>);<br>
 *   <font color="#008800"><i>// Convert HTML to PDF</i></font><br>
 *   <font color="#EE00EE">$pdf</font>-><font color="#0000FF">render</font>();<br>
 *   <font color="#008800"><i>// Output PDF. The file name is suggested to the browser.</i></font><br>
 *   <font color="#EE00EE">$pdf</font>-><font color="#0000FF">output</font>(<b>WKPDF</b>::<font color="#EE00EE">$PDF_EMBEDDED</font>,<font color="#FF0000">'sample.pdf'</font>);<br>
 * @version
 *   0.0 Chris - Created class.<br>
 *   0.1 Chris - Variable paths fixes.<br> 
 *   0.2 Chris - Better error handlng (via exceptions).<br>
 * <font color="#FF0000"><b>IMPORTANT: Make sure that there is a folder in %LIBRARY_PATH%/tmp that is writable!</b></font>
 * <br><br>
 * <b>Features/Bugs/Contact</b><br> 
 * Found a bug? Want a modification? Contact me at <a href="mailto:uuf6429@gmail.com">uuf6429@gmail.com</a> or <a href="mailto:contact@covac-software.com">contact@covac-software.com</a>...
 *   guaranteed to get a reply within 2 hours at most (daytime GMT+1).
 */
class WKPDF {

    /**
     * Private use variables.
     */
    private $html = '';
    private $cmd = '';
    private $tmp = '';
    private $tmp_footer = '';
    private $tmp_header = '';
    private $pdf = '';
    private $uid = '';
    private $rtoken = '';
    private $status = '';
    private $orient = 'Portrait';
    private $size = 'A4';
    private $toc = false;
    private $copies = 1;
    private $grayscale = false;
    private $title = '';
    private static $cpu = '';
    private $is_image = false;
    private $zoom = 0.75;
    private $margin_top = '20';
    private $margin_rigth = '10';
    private $margin_bottom = '20';
    private $margin_left = '10';
    private $footer_spacing = '10';
    private $header_spacing = '15';

    /**
     * Advanced execution routine.
     * @param string $cmd The command to execute.
     * @param string $input Any input not in arguments.
     * @return array An array of execution data; stdout, stderr and return "error" code.
     */
    private static function _pipeExec($cmd, $input = '') {
        $proc = proc_open($cmd, array(0 => array('pipe', 'r'), 1 => array('pipe', 'w'), 2 => array('pipe', 'w')), $pipes);
        fwrite($pipes[0], $input);
        fclose($pipes[0]);
        $stdout = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[2]);
        $rtn = proc_close($proc);
        return array(
            'stdout' => $stdout,
            'stderr' => $stderr,
            'return' => $rtn
        );
    }

    /**
     * Function that attempts to return the kind of CPU.
     * @return string CPU kind ('amd64' or 'i386').
     */
    private static function _getCPU() {
        return 'amd64';
    }

    /**
     * Force the client to download PDF file when finish() is called.
     */
    public static $PDF_DOWNLOAD = 'D';
    public static $IMAGE_DOWNLOAD = 'ID';

    /**
     * Returns the PDF file as a string when finish() is called.
     */
    public static $PDF_ASSTRING = 'S';

    /**
     * When possible, force the client to embed PDF file when finish() is called.
     */
    public static $PDF_EMBEDDED = 'I';

    /**
     * PDF file is saved into the server space when finish() is called. The path is returned.
     */
    public static $PDF_SAVEFILE = 'F';

    /**
     * PDF generated as landscape (vertical).
     */
    public static $PDF_PORTRAIT = 'Portrait';

    /**
     * PDF generated as landscape (horizontal).
     */
    public static $PDF_LANDSCAPE = 'Landscape';

    /**
     * Constructor: initialize command line and reserve temporary file.
     */
    public function __construct() {
        //       $this->cmd = $GLOBALS['WKPDF_BASE_PATH'] . 'wkhtmltopdf-' . self::_getCPU();
//        if (!file_exists($this->cmd)) {
        $this->cmd = "wkhtmltopdf";
//        } else {
//            chmod($this->cmd, 0755);
//        }
        //throw new Exception('WKPDF static executable "' . htmlspecialchars($this->cmd, ENT_QUOTES) . '" was not found.');
        do {
            $rand = mt_rand();
            $this->tmp = $GLOBALS['WKPDF_BASE_PATH'] . 'tmp/' . $rand . '.html';
            $this->tmp_footer = $GLOBALS['WKPDF_BASE_PATH'] . 'tmp/' . $rand . '_footer.html';
            $this->tmp_header = $GLOBALS['WKPDF_BASE_PATH'] . 'tmp/' . $rand . '_header.html';
        } while (file_exists($this->tmp));
    }

    /**
     * Set orientation, use constants from this class.
     * By default orientation is portrait.
     * @param string $mode Use constants from this class.
     */
    public function set_orientation($mode) {
        $this->orient = $mode;
    }

    public function set_uid($uid) {
        $this->uid = $uid;
    }

    public function set_rtoken($rtoken) {
        $this->rtoken = $rtoken;
    }

    public function set_nomargin() {
        $this->margin_top = 0;
        $this->margin_rigth = 0;
        $this->margin_bottom = 0;
        $this->margin_left = 0;
        $this->footer_spacing = 0;
        $this->header_spacing = 0;
    }

    /**
     * Set page/paper size.
     * By default page size is A4.
     * @param string $size Formal paper size (eg; A4, letter...)
     */
    public function set_page_size($size) {
        $this->size = $size;
    }

    /**
     * Set zoom.
     * By default page size is 0.75.
     * @param number $zoom (eg; 0<x<1)
     */
    public function set_zoom($zoom) {
        $this->zoom = $zoom;
    }

    /**
     * Whether to automatically generate a TOC (table of contents) or not.
     * By default TOC is disabled.
     * @param boolean $enabled True use TOC, false disable TOC.
     */
    public function set_toc($enabled) {
        $this->toc = $enabled;
    }

    /**
     * Set the number of copies to be printed.
     * By default it is one.
     * @param integer $count Number of page copies.
     */
    public function set_copies($count) {
        $this->copies = $count;
    }

    /**
     * Whether to print in grayscale or not.
     * By default it is OFF.
     * @param boolean True to print in grayscale, false in full color.
     */
    public function set_grayscale($mode) {
        $this->grayscale = $mode;
    }

    public function set_is_image($is_image) {
        $this->is_image = $is_image;
        if ($this->is_image) {
            $this->set_cmd("wkhtmltoimage");
        }
    }

    /**
     * Set PDF title. If empty, HTML <title> of first document is used.
     * By default it is empty.
     * @param string Title text.
     */
    public function set_title($text) {
        $this->title = $text;
    }

    /**
     * Set html content.
     * @param string $html New html content. It *replaces* any previous content.
     */
    public function set_html($html) {
        $this->html = $html;
        file_put_contents($this->tmp, $html);
    }

    /**
     * Set header content.
     * @param string $html New html content. It *replaces* any previous content.
     */
    public function set_header($html) {
        file_put_contents($this->tmp_header, $html);
    }

    /**
     * Set footer content.
     * @param string $html New footer content. It *replaces* any previous content.
     */
    public function set_footer($html) {
        file_put_contents($this->tmp_footer, $html);
    }

    /**
     * Returns WKPDF print status.
     * @return string WPDF print status.
     */
    public function get_status() {
        return $this->status;
    }

    /**
     * Returns WKPDF print status.
     * @return string WPDF print status.
     */
    public function set_cmd($c) {
        $this->cmd = $c;
    }

    /**
     * Attempts to return the library's full help.
     * @return string WKHTMLTOPDF HTML help.
     */
    public function get_help() {
        $tmp = self::_pipeExec('"' . $this->cmd . '" --extended-help');
        return $tmp['stdout'];
    }

    /**
     * Convert HTML to PDF.
     */
    public function render() {// $d for fast debug purpose ... $d = false
//        echo basename($this->tmp);
//        echo file_get_contents($this->tmp);
//        die;
        $web = $GLOBALS['WKPDF_BASE_SITE'] . 'content/themes/eranker/libs/other/wkhtmltopdf/tmp/' . basename($this->tmp);
//if($d){ echo $web; exit(); } // for fast debug purpose 
        $footer = $GLOBALS['WKPDF_BASE_SITE'] . 'content/themes/eranker/libs/other/wkhtmltopdf/tmp/' . str_replace('.html', '_footer.html', basename($this->tmp));
        $header = $GLOBALS['WKPDF_BASE_SITE'] . 'content/themes/eranker/libs/other/wkhtmltopdf/tmp/' . str_replace('.html', '_header.html', basename($this->tmp));

        if ($this->footer_spacing == 0) {
            $footerline = " --footer-line ";
        } else {
            $footerline = "";
        }

        if ($this->header_spacing == 0) {
            $headerline = " --header-line ";
        } else {
            $headerline = "";
        }


        //wkhtmltopdf --disable-smart-shrinking --load-error-handling skip
        // --no-stop-slow-scripts --zoom 0.75 --enable-plugins --javascript-delay 5000 
        // --viewport-size 1920 --page-width 1920 --page-size a4 http://www.eranker.com/vacarescu.ro/2525?pdf=1  output.pdf   


        $c = '"' . $this->cmd . '" '
//                . (($this->copies > 1) ? ' --copies ' . $this->copies : '')                              // number of copies
                . ' --header-html "' . $header . '" '
                . ' --footer-html "' . $footer . '" '
                . ' --footer-spacing ' . $this->footer_spacing . ' ' . $headerline . ' --header-spacing ' . $this->header_spacing . ' -L ' . $this->margin_left . ' -R ' . $this->margin_rigth . ' -T ' . $this->margin_top . ' -B ' . $this->margin_bottom . '  '
                . ' --enable-smart-shrinking --javascript-delay 2000 --stop-slow-scripts --viewport-size 1100 --page-width 900 --page-size A4 --zoom 1'
                . ' ' . $footerline . ' --load-error-handling skip '  // page size
                . ($this->toc ? ' toc' : '')                                                       // table of contents
                . ($this->grayscale ? ' --grayscale' : '')                                                   // grayscale
                . ' "' . $web . '" -';                                                                                                // URL and optional to write to STDOUT

        if ($this->is_image) {
            $c = '"' . $this->cmd . '" --zoom 0.9 --quality 90 --page-width 960  --stop-slow-scripts --load-error-handling skip "' . $web . '" -';
        }
        if (isset($_GET['debug']) && $_GET['debug'] == 1) {
            echo $c;
            exit;
        }

        $this->pdf = self::_pipeExec($c);
//        if (strpos(strtolower($this->pdf['stderr']), 'error') !== false)
//            throw new Exception('WKPDF system error: <pre>' . $this->pdf['stderr'] . '</pre>');
        if ($this->pdf['stdout'] == '')
            throw new Exception('WKPDF didn\'t return any data. <pre>' . $this->pdf['stderr'] . ' | ' . $this->pdf['stdout'] . '</pre>');
//        if (((int) $this->pdf['return']) > 1)
//            throw new Exception('WKPDF shell error, return code ' . (int) $this->pdf['return'] . '.');
        $this->status = $this->pdf['stderr'];
        $this->pdf = $this->pdf['stdout'];
        unlink($this->tmp);
    }

    /**
     * Return PDF with various options.
     * @param string $mode How two output (constants from this same class).
     * @param string $file The PDF's filename (the usage depends on $mode.
     * @return string|boolean Depending on $mode, this may be success (boolean) or PDF (string).
     */
    public function output($mode, $file) {
        switch ($mode) {
            case self::$PDF_DOWNLOAD:
                if (!headers_sent()) {
                    header('Content-Description: File Transfer');
                    header('Cache-Control: public, must-revalidate, max-age=0'); // HTTP/1.1
                    header('Pragma: public');
                    header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date in the past
                    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
                    // force download dialog
                    header('Content-Type: application/force-download');
                    header('Content-Type: application/octet-stream', false);
                    header('Content-Type: application/download', false);
                    header('Content-Type: application/pdf', false);
                    // use the Content-Disposition header to supply a recommended filename
                    header('Content-Disposition: attachment; filename="' . basename($file) . '";');
                    header('Content-Transfer-Encoding: binary');
                    header('Content-Length: ' . strlen($this->pdf));
                    echo $this->pdf;
                } else {
                    throw new Exception('WKPDF download headers were already sent.');
                }
                break;
            case self::$IMAGE_DOWNLOAD:
                if (!headers_sent()) {
                    header('Content-Description: File Transfer');
                    header('Cache-Control: public, must-revalidate, max-age=0'); // HTTP/1.1
                    header('Pragma: public');
                    header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date in the past
                    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
                    // force download dialog
                    header('Content-Type: application/force-download');
                    header('Content-Type: application/octet-stream', false);
                    header('Content-Type: application/download', false);
                    header('Content-Type: image/jpeg', false);
                    // use the Content-Disposition header to supply a recommended filename
                    header('Content-Disposition: attachment; filename="' . basename($file) . '";');
                    header('Content-Transfer-Encoding: binary');
                    header('Content-Length: ' . strlen($this->pdf));
                    echo $this->pdf;
                } else {
                    throw new Exception('WKPDF download headers were already sent.');
                }
                break;
            case self::$PDF_ASSTRING:
                return $this->pdf;
                break;
            case self::$PDF_EMBEDDED:
                if (!headers_sent()) {
                    header('Content-Type: application/pdf');
                    header('Cache-Control: public, must-revalidate, max-age=0'); // HTTP/1.1
                    header('Pragma: public');
                    header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date in the past
                    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
                    header('Content-Length: ' . strlen($this->pdf));
                    header('Content-Disposition: inline; filename="' . basename($file) . '";');
                    echo $this->pdf;
                } else {
                    throw new Exception('WKPDF embed headers were already sent.');
                }
                break;
            case self::$PDF_SAVEFILE:
                return file_put_contents($file, $this->pdf);
                break;
            default:
                throw new Exception('WKPDF invalid mode "' . htmlspecialchars($mode, ENT_QUOTES) . '".');
        }
        return false;
    }

}
