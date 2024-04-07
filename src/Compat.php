<?php declare(strict_types=1);

namespace Freya;

use Symfony\Component\Mime;

require_once __DIR__ . '/../vendor/autoload.php';

class Compat {
    public static function getContentType(string $path): string
    {
        // Todo: Change the Content-Type to 'application/octet-stream'
        $unknown = 'text/plain';

        $ext = pathinfo($path, PATHINFO_EXTENSION);
        if ($ext === '') {
            return $unknown;
        }

        // Emulate freo_mime() behavior
        $override = [
            'application/javascript' => [
                'js', // text/javascript application/javascript application/x-javascript
            ],
            'application/mac-binhex40' => [
                'hqx', // application/stuffit application/mac-binhex40
            ],
            'application/octet-stream' => [
                'bpk', //
                'class', // application/java application/java-byte-code application/java-vm application/x-java application/x-java-class application/x-java-vm
                'dist', //
                'distz', //
                'dmg', // application/x-apple-diskimage
                'dms', //
                'dump', //
                'elc', //
                'iso', // application/x-cd-image application/x-dreamcast-rom application/x-gamecube-iso-image application/x-gamecube-rom application/x-iso9660-image application/x-saturn-rom application/x-sega-cd-rom application/x-sega-pico-rom application/x-wbfs application/x-wia application/x-wii-iso-image application/x-wii-rom
                'lha', // application/x-lha application/x-lzh-compressed
                'lzh', // application/x-lha application/x-lzh-compressed
                'pkg', // application/x-xar
                'so', // application/x-sharedlib
            ],
            'application/ogg' => [
                'ogg', // audio/ogg audio/vorbis audio/x-flac+ogg audio/x-ogg audio/x-oggflac audio/x-speex+ogg audio/x-vorbis audio/x-vorbis+ogg video/ogg video/x-ogg video/x-theora video/x-theora+ogg
            ],
            'application/pgp-encrypted' => [
                'pgp', // application/pgp application/pgp-encrypted application/pgp-keys application/pgp-signature
            ],
            'application/pgp-signature' => [
                'asc', // application/pgp application/pgp-encrypted application/pgp-keys application/pgp-signature text/plain
            ],
            'application/pls+xml' => [
                'pls', // application/pls application/pls+xml audio/scpls audio/x-scpls
            ],
            'application/postscript' => [
                'ai', // application/illustrator application/postscript application/vnd.adobe.illustrator
            ],
            'application/smil+xml' => [
                'smi', // application/smil application/smil+xml application/x-sami
                'smil', // application/smil application/smil+xml
            ],
            'application/vnd.commonspace' => [
                'cst', // application/x-director
            ],
            'application/vnd.curl' => [
                'curl', // text/vnd.curl
            ],
            'application/vnd.hzn-3d-crossword' => [
                'x3d', // model/x3d+xml
            ],
            'application/vnd.lotus-1-2-3' => [
                '123', // application/lotus123 application/vnd.lotus-1-2-3 application/wk1 application/x-123 application/x-lotus123 zz-application/zz-winassoc-123
            ],
            'application/vnd.ms-excel' => [
                'xla', // application/msexcel application/vnd.ms-excel application/x-msexcel zz-application/zz-winassoc-xls
                'xlc', // application/msexcel application/vnd.ms-excel application/x-msexcel zz-application/zz-winassoc-xls
                'xlm', // application/msexcel application/vnd.ms-excel application/x-msexcel zz-application/zz-winassoc-xls
                'xlt', // application/msexcel application/vnd.ms-excel application/x-msexcel zz-application/zz-winassoc-xls
                'xlw', // application/msexcel application/vnd.ms-excel application/x-msexcel zz-application/zz-winassoc-xls
            ],
            'application/vnd.ms-powerpoint' => [
                'pot', // application/mspowerpoint application/powerpoint application/vnd.ms-powerpoint application/x-mspowerpoint text/x-gettext-translation-template text/x-pot
                'pps', // application/mspowerpoint application/powerpoint application/vnd.ms-powerpoint application/x-mspowerpoint
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
            'application/x-ace-compressed' => [
                'ace', // application/x-ace application/x-ace-compressed
            ],
            'application/x-bzip' => [
                'bz', // application/bzip2 application/x-bzip application/x-bzip2
            ],
            'application/x-bzip2' => [
                'bz2', // application/x-bz2 application/bzip2 application/x-bzip application/x-bzip2
            ],
            'application/x-chess-pgn' => [
                'pgn', // application/vnd.chess-pgn application/x-chess-pgn
            ],
            'application/x-futuresplash' => [
                'spl', // application/futuresplash application/vnd.adobe.flash.movie application/x-futuresplash application/x-shockwave-flash
            ],
            'application/x-msdownload' => [
                'exe', // application/x-ms-dos-executable application/x-msdos-program application/x-msdownload
            ],
            'application/x-msmetafile' => [
                'wmf', // application/wmf application/x-msmetafile application/x-wmf image/wmf image/x-win-metafile image/x-wmf
            ],
            'application/x-mspublisher' => [
                'pub', // application/vnd.ms-publisher application/x-mspublisher
            ],
            'application/x-pkcs12' => [
                'p12', // application/pkcs12 application/x-pkcs12
                'pfx', // application/pkcs12 application/x-pkcs12
            ],
            'application/x-shockwave-flash' => [
                'swf', // application/futuresplash application/vnd.adobe.flash.movie application/x-shockwave-flash
            ],
            'application/xspf+xml' => [
                'xspf', // application/x-xspf+xml application/xspf+xml
            ],
            'audio/mpeg' => [
                'mp2', // audio/mp2 audio/mpeg audio/x-mp2 video/mpeg video/mpeg-system video/x-mpeg video/x-mpeg-system video/x-mpeg2
                'mpga', // audio/mp3 audio/mpeg audio/x-mp3 audio/x-mpeg audio/x-mpg
            ],
            'audio/x-aiff' => [
                'aifc', // audio/x-aifc audio/x-aiff audio/x-aiffc
            ],
            'audio/x-ms-wax' => [
                'wax', // application/x-ms-asx audio/x-ms-asx audio/x-ms-wax video/x-ms-wax video/x-ms-wmx video/x-ms-wvx
            ],
            'audio/x-pn-realaudio' => [
                'ra', // audio/vnd.m-realaudio audio/vnd.rn-realaudio audio/x-pn-realaudio audio/x-realaudio
                'ram', // application/ram audio/x-pn-realaudio
            ],
            'audio/x-wav' => [
                'wav', // audio/wav audio/vnd.wave audio/wave audio/x-wav
            ],
            'chemical/x-pdb' => [
                'pdb', // application/vnd.palm application/x-aportisdoc application/x-palm-database application/x-pilot
            ],
            'image/g3fax' => [
                'g3', // image/fax-g3 image/g3fax
            ],
            'image/vnd.adobe.photoshop' => [
                'psd', // application/photoshop application/x-photoshop image/photoshop image/psd image/vnd.adobe.photoshop image/x-photoshop image/x-psd
            ],
            'image/x-icon' => [
                'ico', // application/ico image/ico image/icon image/vnd.microsoft.icon image/x-ico image/x-icon text/ico
            ],
            'image/x-pcx' => [
                'pcx', // image/vnd.zbrush.pcx image/x-pcx
            ],
            'text/calendar' => [
                'ics', // application/ics text/calendar text/x-vcalendar
            ],
            'text/troff' => [
                'man', // application/x-troff-man text/troff
                'roff', // application/x-troff text/troff text/x-troff
                't', // application/x-perl application/x-troff text/troff text/x-perl text/x-troff
                'tr', // application/x-troff text/troff text/x-troff
            ],
            'text/x-java-source' => [
                'java', // text/x-java text/x-java-source
            ],
            'text/x-vcalendar' => [
                'vcs', // application/ics text/calendar text/x-vcalendar
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
            'video/vnd.vivo' => [
                'viv', // video/vivo video/vnd.vivo
            ],
            'video/x-fli' => [
                'fli', // video/fli video/x-fli video/x-flic
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
            'video/x-msvideo' => [
                'avi', // video/avi video/divx video/msvideo video/vnd.divx video/x-avi video/x-msvideo
            ],
        ];
        $guess = new Mime\MimeTypes($override);
        $mimetypes = $guess->getMimeTypes($ext);

        return !empty($mimetypes) ? $mimetypes[0] : $unknown;
    }
}
