<?php

namespace src\core;

class Message
{
    private string $type;
    private string $text;

    public function __toString(): string
    {
        return $this->render();
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    public function primary(string $message): self
    {
        $this->type = 'primary';
        $this->text = $this->filter($message);
        return $this;
    }

    public function secondary(string $message): self
    {
        $this->type = 'secondary';
        $this->text = $this->filter($message);
        return $this;
    }

    public function success(string $message): self
    {
        $this->type = 'success';
        $this->text = $this->filter($message);
        return $this;
    }

    public function danger(string $message): self
    {
        $this->type = 'danger';
        $this->text = $this->filter($message);
        return $this;
    }

    public function warning(string $message): self
    {
        $this->type = 'warning';
        $this->text = $this->filter($message);
        return $this;
    }

    public function info(string $message): self
    {
        $this->type = 'info';
        $this->text = $this->filter($message);
        return $this;
    }

    public function light(string $message): self
    {
        $this->type = 'light';
        $this->text = $this->filter($message);
        return $this;
    }

    public function dark(string $message): self
    {
        $this->type = 'dark';
        $this->text = $this->filter($message);
        return $this;
    }

    /**
     * @return string
     */
    public function render(): string
    {
        return <<<MSG
<div class="alert alert-{$this->type} alert-dismissible" role="alert">
    <div>{$this->text}</div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
MSG;
    }

    public function flash(): void
    {
        (new Session())->set('flash', $this);
    }

    private function filter(string $message): string
    {
        return filter_var($message, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }
}
