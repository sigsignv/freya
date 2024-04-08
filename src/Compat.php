<?php declare(strict_types=1);

namespace Freya;

use Symfony\Component\Mime;

require_once __DIR__ . '/../vendor/autoload.php';

class Compat {
    public static function getContentType(string $path): string
    {
        $unknown = 'application/octet-stream';

        $ext = pathinfo($path, PATHINFO_EXTENSION);
        if ($ext === '') {
            return $unknown;
        }

        // Emulate freo_mime() behavior
        $override = [
            'application/mac-binhex40' => [
                'hqx', // application/stuffit application/mac-binhex40
            ],
            'application/octet-stream' => [
                'bpk', //
                'dist', //
                'distz', //
                'dms', //
                'dump', //
                'elc', //
            ],
            'application/vnd.commonspace' => [
                'cst', // application/x-director
            ],
            'application/vnd.hzn-3d-crossword' => [
                'x3d', // model/x3d+xml
            ],
            'application/vnd.ms-works' => [
                'wks', // application/lotus123 application/vnd.lotus-1-2-3 application/vnd.ms-works application/wk1 application/x-123 application/x-lotus123 zz-application/zz-winassoc-123
            ],
            'application/vnd.oasis.opendocument.text-master' => [
                'otm', //
            ],
            'application/vnd.visio' => [
                'vst', // application/tga application/vnd.visio application/x-targa application/x-tga image/targa image/tga image/x-icb image/x-targa image/x-tga
            ],
            'application/x-shockwave-flash' => [
                'swf', // application/futuresplash application/vnd.adobe.flash.movie application/x-shockwave-flash
            ],
            'audio/mpeg' => [
                'mp2', // audio/mp2 audio/mpeg audio/x-mp2 video/mpeg video/mpeg-system video/x-mpeg video/x-mpeg-system video/x-mpeg2
                'mpga', // audio/mp3 audio/mpeg audio/x-mp3 audio/x-mpeg audio/x-mpg
            ],
            'audio/x-ms-wax' => [
                'wax', // application/x-ms-asx audio/x-ms-asx audio/x-ms-wax video/x-ms-wax video/x-ms-wmx video/x-ms-wvx
            ],
            'audio/x-pn-realaudio' => [
                'ra', // audio/vnd.m-realaudio audio/vnd.rn-realaudio audio/x-pn-realaudio audio/x-realaudio
                'ram', // application/ram audio/x-pn-realaudio
            ],
            'chemical/x-pdb' => [
                'pdb', // application/vnd.palm application/x-aportisdoc application/x-palm-database application/x-pilot
            ],
            'image/x-pcx' => [
                'pcx', // image/vnd.zbrush.pcx image/x-pcx
            ],
            'video/3gpp' => [
                '3gp', // audio/3gpp audio/3gpp-encrypted audio/x-rn-3gpp-amr audio/x-rn-3gpp-amr-encrypted audio/x-rn-3gpp-amr-wb audio/x-rn-3gpp-amr-wb-encrypted video/3gp video/3gpp video/3gpp-encrypted
            ],
            'video/3gpp2' => [
                '3g2', // audio/3gpp2 video/3gpp2
            ],
            'video/jpm' => [
                'jpgm', // image/jpm video/jpm
                'jpm', // image/jpm video/jpm
            ],
            'video/x-ms-asf' => [
                'asf', // application/vnd.ms-asf video/x-ms-asf video/x-ms-asf-plugin video/x-ms-wm
                'asx', // application/x-ms-asx audio/x-ms-asx video/x-ms-asf video/x-ms-wax video/x-ms-wmx video/x-ms-wvx
            ],
            'video/x-ms-wmv' => [
                'wmv', // audio/x-ms-wmv video/x-ms-wmv
            ],
            'video/x-ms-wmx' => [
                'wmx', // application/x-ms-asx audio/x-ms-asx video/x-ms-wax video/x-ms-wmx video/x-ms-wvx
            ],
            'video/x-ms-wvx' => [
                'wvx', // application/x-ms-asx audio/x-ms-asx video/x-ms-wax video/x-ms-wmx video/x-ms-wvx
            ],
        ];
        $guess = new Mime\MimeTypes($override);
        $mimetypes = $guess->getMimeTypes($ext);

        return !empty($mimetypes) ? $mimetypes[0] : $unknown;
    }
}
