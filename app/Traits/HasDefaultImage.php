<?php

namespace App\Traits;

trait HasDefaultImage
{
    /**
     * Retorna a URL da imagem com fallback para imagem padrão
     */
    public function getImageUrl()
    {
        if (!$this->foto) {
            return "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAOEAAADhCAMAAAAJbSJIAAAAclBMVEX///8AAACoqKj8/PwEBAT5+fn29vbl5eXz8/O1tbV1dXXY2NhTU1MiIiLp6enh4eHFxcVtbW2dnZ3Ozs4uLi43NzcbGxtJSUmXl5eRkZGJiYmsrKx5eXnR0dFCQkJXV1eBgYEQEBAnJydmZmYcHBy7u7vQ67L1AAAFXUlEVORw0KGgoAAAABJRU5ErkJggg==";
        }
        
        // Garantir que a URL seja absoluta
        return config('app.url') . '/storage/' . $this->foto;
    }
}