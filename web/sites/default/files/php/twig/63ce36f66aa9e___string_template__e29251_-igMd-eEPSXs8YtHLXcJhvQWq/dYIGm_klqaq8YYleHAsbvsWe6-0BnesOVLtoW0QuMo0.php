<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* __string_template__e29251fc129b4b438442a5f8ba5c9e8c */
class __TwigTemplate_37c2d283e33816fa720ed65d66b66fbc extends \Twig\Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
        $this->sandbox = $this->env->getExtension('\Twig\Extension\SandboxExtension');
        $this->checkSecurity();
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        $context["start_date"] = $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["node"] ?? null), "field_start_date", [], "any", false, false, true, 1), 0, [], "any", false, false, true, 1), "value", [], "any", false, false, true, 1), 1, $this->source);
        // line 2
        $context["end_date"] = $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["node"] ?? null), "field_end", [], "any", false, false, true, 2), 0, [], "any", false, false, true, 2), "value", [], "any", false, false, true, 2), 2, $this->source);
        // line 3
        $context["difference"] = (twig_date_format_filter($this->env, $this->sandbox->ensureToStringAllowed(($context["end_date"] ?? null), 3, $this->source), "U") - twig_date_format_filter($this->env, $this->sandbox->ensureToStringAllowed(($context["start_date"] ?? null), 3, $this->source), "U"));
        // line 4
        $context["differenceDays"] = twig_round((((($context["difference"] ?? null) / 60) / 60) / 24), 0, "ceil");
        // line 5
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ((($context["differenceDays"] ?? null) + 1) *  -1), "html", null, true);
    }

    public function getTemplateName()
    {
        return "__string_template__e29251fc129b4b438442a5f8ba5c9e8c";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  47 => 5,  45 => 4,  43 => 3,  41 => 2,  39 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "__string_template__e29251fc129b4b438442a5f8ba5c9e8c", "");
    }
    
    public function checkSecurity()
    {
        static $tags = array("set" => 1);
        static $filters = array("raw" => 1, "date" => 3, "round" => 4, "escape" => 5);
        static $functions = array();

        try {
            $this->sandbox->checkSecurity(
                ['set'],
                ['raw', 'date', 'round', 'escape'],
                []
            );
        } catch (SecurityError $e) {
            $e->setSourceContext($this->source);

            if ($e instanceof SecurityNotAllowedTagError && isset($tags[$e->getTagName()])) {
                $e->setTemplateLine($tags[$e->getTagName()]);
            } elseif ($e instanceof SecurityNotAllowedFilterError && isset($filters[$e->getFilterName()])) {
                $e->setTemplateLine($filters[$e->getFilterName()]);
            } elseif ($e instanceof SecurityNotAllowedFunctionError && isset($functions[$e->getFunctionName()])) {
                $e->setTemplateLine($functions[$e->getFunctionName()]);
            }

            throw $e;
        }

    }
}
