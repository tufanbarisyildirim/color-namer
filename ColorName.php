<?php
    include(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'ColorInfo.php');
    /**
    * ColorNamer 1.0
    * Copyright 2011 Tufan Baris YILDIRIM
    * This Class is the php implentation of "Name That Color Javascript" Color dataset from  (http://chir.ag/projects/ntc/) projects.
    *
    * Website: http://me.tufyta.com
    *
    * $Id: ColorName.php 2011-07-14 05:14:10Z tfnyldrm $
    */
    class ColorName 
    {
        public static $colors = array();
        /**
        * Run explain_me for each color in self::$colors
        */
        public static function Prepare($data_file)
        {
            if(is_file($data_file))
            {
                self::$colors = unserialize(file_get_contents($data_file));
                #passived. ExplainMe rewritten in ColorInfo::__wakeup() method;
                // array_map('ColorName::ExplainMe',self::$colors);
            }
            else
                throw new Exception("{$data_file} is not a regular file");
        } 

        /**
        *  Check self::colors is ready for use    
        *  @return boolean
        */
        private static function IsReady()
        {
            return self::$colors[0] instanceof ColorInfo;
        }

        /**
        * Explain Colors For using, set the color Rgb, and Hsl value.
        * if you dont want more io you can rewrite self::$colors by using var_export() and  isReady() will return true.
        * 
        * @param mixed $color
        * @return array
        */
        public static function ExplainMe(ColorInfo &$color)
        {                             
            // $color = new ColorInfo($color);
            $color->SetColorVars();
            return $color;
        }

        /**
        * Get rgb values of color
        * 
        * What is RGB ?
        * =============
        * @link http://en.wikipedia.org/wiki/RGB
        * @param mixed $hex
        * @param mixed $char_index
        * @return array
        */
        public static function GetRgb($hex,$char_index = true)
        {
            $rgb_hex_array =  str_split($hex,2);
            $rgb_dec_array = array_map('hexdec',$rgb_hex_array);

            if($char_index)
                return array('r' => $rgb_dec_array[0],'g' => $rgb_dec_array[1],'b' => $rgb_dec_array[2]);
            else
                return $rgb_dec_array;
        }

        public static function multiple($val)
        {
            return $val * 255;   
        }
        
        public static function  divide($val)
        {
            return $val / 255;
        }

        /**
        * Get hsl values of color
        * 
        * What is HSL ?
        * =============
        * @link  http://en.wikipedia.org/wiki/HSL_and_HSV
        * @param mixed $color
        * @param mixed $char_index
        * @return array
        */
        public static function GetHsl($color,$char_index = true)
        {   
            $hsl = array('h' => 0,'s' => 0,'l' => 0);

            $rgb = self::GetRgb($color);
            $rgb = array_map('ColorName::divide',$rgb);

            $min_color = min($rgb);
            $max_color = max($rgb);

            $egim = $max_color - $min_color;

            $hsl['l'] = ($min_color + $max_color) / 2;


            if($hsl['l'] > 0 && $hsl['l'] < 1)
                $hsl['s'] = $egim / ($hsl['l'] < 0.5 ? (2 * $hsl['l']) : (2 - 2 * $hsl['l']));

            if($egim > 0)
            {
                if ($max_color == $rgb['r'] && $max_color != $rgb['g']) 
                    $hsl['h'] += ($rgb['g'] - $rgb['b']) / $egim;
                if ($max_color == $rgb['g'] && $max_color != $rgb['b']) 
                    $hsl['h'] += (2 + ($rgb['b'] - $rgb['r']) / $egim);
                if ($max_color == $rgb['b'] && $max_color != $rgb['r']) 
                    $hsl['h'] += (4 + ($rgb['r'] - $rgb['g']) / $egim);
                $hsl['h'] /= 6;
            }
            $hsl  = array_map("ColorName::multiple",$hsl);
 
            if($char_index)
                return $hsl;
            else
                return array_values($hsl);
        }
        
        public static function fix_code($color)
        {
            $color = strtoupper( str_replace( '#','',trim($color)));

            if(preg_match('/^[A-F0-9]{3}$/',$color))
                $color = $color[0] . $color[0] . $color[1] . $color[1] . $color[2] . $color[2];            
            if(!preg_match('/^[A-F0-9]{6}$/',$color))
                throw new Exception('invalid color format : ' . $color);
                
                return $color;
        }

        /**
        * Find Name of the color!
        * 
        * @param mixed $color
        * @return ColorInfo
        */
        public static function GetInfo($color)
        {
            if(!self::isReady())
                throw new Exception('colors are not ready to use. please run ColorName::Prepare() first; : ' . $color);  

          /*  if(is_array($color))                                                             
                $color = self::hex_me($color);   */

            $color  = self::fix_code($color);

            $hsl = self::GetHsl($color);
            $rgb = self::GetRgb($color);

            $df = -1;
            $cl = -1;

            $percent = 0; 



            foreach (self::$colors as $i => $colorInfo)
            {
                if($color == $colorInfo->code)
                {
                    $percent =  100;
                    return $colorInfo;
                }

                $ndf1 = pow($rgb['r'] - $colorInfo->r, 2) + pow($rgb['g'] - $colorInfo->g, 2) + pow($rgb['b'] - $colorInfo->b, 2);
                $ndf2 = pow($hsl['h'] - $colorInfo->h, 2) + pow($hsl['s'] -$colorInfo->s, 2) + pow($hsl['l'] - $colorInfo->l, 2);
                $ndf = $ndf1 + $ndf2 * 2;
                if($df < 0 || $df > $ndf)
                {
                    $df = $ndf;
                    $cl = $i;
                    if($df < 100)
                        $percent = 100 - $df;
                    else
                        $percent = abs($df) % 100;
                }  
            }

            if($cl < 0)
                throw new Exception('invalid color> : ' . $color,E_USER_WARNING);
            else
            {
                $colorInfo = self::$colors[$cl];
                $colorInfo->similarity =  $percent;
                return $colorInfo;
            } 

            #ide Hack.
            return new ColorInfo();  
        }

        /**
        * Convert Decimal to Hex with min 2 char
        * 
        * @param mixed $dec
        * @return string
        */
        public static function ColorDechex($dec)
        {
            return str_pad(strtoupper(dechex($dec)),2,'0',STR_PAD_LEFT);
        }

        /**
        * Convert a rgb array to color Hex Code 
        * 
        * @param array $rgb
        * @return string
        */
        public static function HexMe(array $rgb)
        {
            if(count($rgb) > 3)
                $rgb = array_slice($rgb,0,3);
            return implode('',array_map('ColorName::ColorDechex',$rgb));
        }

    }

?>