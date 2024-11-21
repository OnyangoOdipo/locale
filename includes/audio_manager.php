<?php
class AudioManager {
    private $audioBasePath;
    
    public function __construct() {
        $this->audioBasePath = dirname(__DIR__) . '/audio';
    }
    
    public function getAudioPath($dialect, $category, $filename) {
        $path = "{$this->audioBasePath}/{$dialect}/{$category}/{$filename}.mp3";
        return file_exists($path) ? $path : false;
    }
    
    public function checkMissingAudio() {
        $missing = [];
        $required = [
            'kikuyu' => [
                'alphabet' => ['i_sound', 'u_sound', 'ng_sound'],
                'greetings' => ['uhoro_wa_ruciini', 'ni_mwega', 'ni_ndakena'],
                // Add more required files
            ],
            'luo' => [
                'alphabet' => ['ny_sound', 'ch_sound', 'ng_sound'],
                'greetings' => ['oyawore', 'antie_maber', 'erokamano'],
                // Add more required files
            ],
            // Add other dialects
        ];
        
        foreach ($required as $dialect => $categories) {
            foreach ($categories as $category => $files) {
                foreach ($files as $file) {
                    $path = $this->getAudioPath($dialect, $category, $file);
                    if (!$path) {
                        $missing[] = "{$dialect}/{$category}/{$file}.mp3";
                    }
                }
            }
        }
        
        return $missing;
    }
} 