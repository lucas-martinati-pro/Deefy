<?php

namespace iutnc\deefy\render;

interface Renderer {
    const int COMPACT = 1, LONG = 2;

    public function render(int $selector) : string;
}