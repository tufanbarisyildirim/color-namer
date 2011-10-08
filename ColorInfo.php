<?php
    /**
    * some changes.
     * ColorNamer 1.0
     * Copyright 2011 Tufan Baris YILDIRIM
     *
     * Website: http://me.tufyta.com
     *
     * $Id: ColorInfo.php 2011-07-14 04:56:12Z tfnyldrm $
     */
    class ColorInfo
    {
        /**
         * Hec Code of Color
         * 
         * @var mixed
         */
        public $code;
        /**
         * Color Name
         * 
         * @var mixed
         */
        public $name;
        /**
         * Similarty between color you given and the nearest color
         * 
         * @var mixed
         */
        public $similarity; 

        public $r;
        public $g;
        public $b;
        public $h;
        public $s;
        public $l;

        public $rgb_hsl;

        /**
         * ColorInfo contructor, it can craete witn an array('FFF','White')
         * 
         * @param mixed $array
         * @return ColorInfo
         */
        public function __construct($array = false)
        {
            if(is_array($array))
            {
                if(isset($array[0]))
                    $this->code = $array[0];
                if(isset($array[1]))
                    $this->name = $array[1];                      
            }
            $this->UnsetVirtuals();
            $this->SetColorVars();
        }

        /**
         * Setup  rgb and hsl values of the color.
         */
        public function SetColorVars()
        {
            $rgb = ColorName::GetRgb($this->code,true);
            $hsl = ColorName::GetHsl($this->code,true);
            $this->rgb_hsl = array_merge($rgb,$hsl);

            return true;
        }

        /**
         * Unset the virtual properties for magical getting #unset r,g,b,h,s,l
         * 
         */
        public function UnsetVirtuals()
        {
            #unset r,g,b,h,s,l
            foreach($this as $property_name => $value)
            {
                if(!isset($property_name[1]))
                    unset($this->$property_name);
            }
        }

        /**
         * Magic method for   r,g,b,h,s,l
         * 
         * @param mixed $var
         */
        public function __get($var)
        {
            if(isset($this->rgb_hsl[$var]))
                return $this->rgb_hsl[$var];

            throw new Exception("Unknown property name : " . $var);
        }

        /**
         * __wakeup() is resetting the empty values.
         * need when color loadinf from a .dat file.
         */
        public function __wakeup()
        {
            $this->UnsetVirtuals();
            $this->SetColorVars();
        }

    }
?>