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

/* retention.html.twig */
class __TwigTemplate_e972fbeac97ccc78f379edb17aee14518090b7241d29041e7897110cecd86dd1 extends Template
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
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        echo "<html>
<head>
    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">
    <style type=\"text/css\">
        ";
        // line 5
        $this->loadTemplate("assets/style.css", "retention.html.twig", 5)->display($context);
        // line 6
        echo "    </style>
</head>
<body class=\"white-bg\">
";
        // line 9
        $context["cp"] = twig_get_attribute($this->env, $this->source, (isset($context["doc"]) || array_key_exists("doc", $context) ? $context["doc"] : (function () { throw new RuntimeError('Variable "doc" does not exist.', 9, $this->source); })()), "company", [], "any", false, false, false, 9);
        // line 10
        $context["name"] = $this->env->getRuntime('Greenter\Report\Filter\DocumentFilter')->getValueCatalog("20", "01");
        // line 11
        echo "<table width=\"100%\">
    <tbody><tr>
        <td style=\"padding:30px; !important\">
            <table width=\"100%\" height=\"200px\" border=\"0\" aling=\"center\" cellpadding=\"0\" cellspacing=\"0\">
                <tbody><tr>
                    <td width=\"50%\" height=\"90\" align=\"center\">
                        <span><img src=\"";
        // line 17
        echo twig_escape_filter($this->env, $this->env->getRuntime('Greenter\Report\Filter\ImageFilter')->toBase64(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["params"]) || array_key_exists("params", $context) ? $context["params"] : (function () { throw new RuntimeError('Variable "params" does not exist.', 17, $this->source); })()), "system", [], "any", false, false, false, 17), "logo", [], "any", false, false, false, 17)), "html", null, true);
        echo "\" height=\"80\" style=\"text-align:center\" border=\"0\"></span>
                    </td>
                    <td width=\"5%\" height=\"40\" align=\"center\"></td>
                    <td width=\"45%\" rowspan=\"2\" valign=\"bottom\" style=\"padding-left:0\">
                        <div class=\"tabla_borde\">
                            <table width=\"100%\" border=\"0\" height=\"200\" cellpadding=\"6\" cellspacing=\"0\">
                                <tbody><tr>
                                    <td align=\"center\">
                                        <span style=\"font-family:Tahoma, Geneva, sans-serif; font-size:29px\" text-align=\"center\">COMPROBANTE DE ";
        // line 25
        echo twig_escape_filter($this->env, (isset($context["name"]) || array_key_exists("name", $context) ? $context["name"] : (function () { throw new RuntimeError('Variable "name" does not exist.', 25, $this->source); })()), "html", null, true);
        echo "</span>
                                        <br>
                                        <span style=\"font-family:Tahoma, Geneva, sans-serif; font-size:19px\" text-align=\"center\">E L E C T R Ó N I C A</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td align=\"center\">
                                        <span style=\"font-size:15px\" text-align=\"center\">R.U.C.: ";
        // line 32
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["cp"]) || array_key_exists("cp", $context) ? $context["cp"] : (function () { throw new RuntimeError('Variable "cp" does not exist.', 32, $this->source); })()), "ruc", [], "any", false, false, false, 32), "html", null, true);
        echo "</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td align=\"center\">
                                        <span style=\"font-size:24px\">";
        // line 37
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["doc"]) || array_key_exists("doc", $context) ? $context["doc"] : (function () { throw new RuntimeError('Variable "doc" does not exist.', 37, $this->source); })()), "serie", [], "any", false, false, false, 37), "html", null, true);
        echo "-";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["doc"]) || array_key_exists("doc", $context) ? $context["doc"] : (function () { throw new RuntimeError('Variable "doc" does not exist.', 37, $this->source); })()), "correlativo", [], "any", false, false, false, 37), "html", null, true);
        echo "</span>
                                    </td>
                                </tr>
                                </tbody></table>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td valign=\"bottom\" style=\"padding-left:0\">
                        <div class=\"tabla_borde\">
                            <table width=\"96%\" height=\"100%\" border=\"0\" border-radius=\"\" cellpadding=\"9\" cellspacing=\"0\">
                                <tbody><tr>
                                    <td align=\"center\">
                                        <strong><span style=\"font-size:15px\">";
        // line 50
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["cp"]) || array_key_exists("cp", $context) ? $context["cp"] : (function () { throw new RuntimeError('Variable "cp" does not exist.', 50, $this->source); })()), "razonSocial", [], "any", false, false, false, 50), "html", null, true);
        echo "</span></strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td align=\"left\">
                                        <strong>Dirección: </strong>";
        // line 55
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["cp"]) || array_key_exists("cp", $context) ? $context["cp"] : (function () { throw new RuntimeError('Variable "cp" does not exist.', 55, $this->source); })()), "address", [], "any", false, false, false, 55), "direccion", [], "any", false, false, false, 55), "html", null, true);
        echo "
                                    </td>
                                </tr>
                                <tr>
                                    <td align=\"left\">
                                        ";
        // line 60
        echo twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["params"]) || array_key_exists("params", $context) ? $context["params"] : (function () { throw new RuntimeError('Variable "params" does not exist.', 60, $this->source); })()), "user", [], "any", false, false, false, 60), "header", [], "any", false, false, false, 60);
        echo "
                                    </td>
                                </tr>
                                </tbody></table>
                        </div>
                    </td>
                </tr>
                </tbody></table>
            <div class=\"tabla_borde\">
                ";
        // line 69
        $context["cl"] = twig_get_attribute($this->env, $this->source, (isset($context["doc"]) || array_key_exists("doc", $context) ? $context["doc"] : (function () { throw new RuntimeError('Variable "doc" does not exist.', 69, $this->source); })()), "proveedor", [], "any", false, false, false, 69);
        // line 70
        echo "                <table width=\"100%\" border=\"0\" cellpadding=\"5\" cellspacing=\"0\">
                    <tbody><tr>
                        <td width=\"60%\" align=\"left\"><strong>Razón Social:</strong>  ";
        // line 72
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["cl"]) || array_key_exists("cl", $context) ? $context["cl"] : (function () { throw new RuntimeError('Variable "cl" does not exist.', 72, $this->source); })()), "rznSocial", [], "any", false, false, false, 72), "html", null, true);
        echo "</td>
                        <td width=\"40%\" align=\"left\"><strong>";
        // line 73
        echo twig_escape_filter($this->env, $this->env->getRuntime('Greenter\Report\Filter\DocumentFilter')->getValueCatalog(twig_get_attribute($this->env, $this->source, (isset($context["cl"]) || array_key_exists("cl", $context) ? $context["cl"] : (function () { throw new RuntimeError('Variable "cl" does not exist.', 73, $this->source); })()), "tipoDoc", [], "any", false, false, false, 73), "06"), "html", null, true);
        echo ":</strong>  ";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["cl"]) || array_key_exists("cl", $context) ? $context["cl"] : (function () { throw new RuntimeError('Variable "cl" does not exist.', 73, $this->source); })()), "numDoc", [], "any", false, false, false, 73), "html", null, true);
        echo "</td>
                    </tr>
                    <tr>
                        <td width=\"60%\" align=\"left\">
                            <strong>Fecha Emisión: </strong>  ";
        // line 77
        echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["doc"]) || array_key_exists("doc", $context) ? $context["doc"] : (function () { throw new RuntimeError('Variable "doc" does not exist.', 77, $this->source); })()), "fechaEmision", [], "any", false, false, false, 77), "d/m/Y"), "html", null, true);
        echo "
                        </td>
                        <td width=\"40%\" align=\"left\"><strong>Dirección: </strong>  ";
        // line 79
        if (twig_get_attribute($this->env, $this->source, (isset($context["cl"]) || array_key_exists("cl", $context) ? $context["cl"] : (function () { throw new RuntimeError('Variable "cl" does not exist.', 79, $this->source); })()), "address", [], "any", false, false, false, 79)) {
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["cl"]) || array_key_exists("cl", $context) ? $context["cl"] : (function () { throw new RuntimeError('Variable "cl" does not exist.', 79, $this->source); })()), "address", [], "any", false, false, false, 79), "direccion", [], "any", false, false, false, 79), "html", null, true);
        }
        echo "</td>
                    </tr>
                    <tr>
                        <td width=\"60%\" align=\"left\"><strong>Régimen: </strong>  ";
        // line 82
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["doc"]) || array_key_exists("doc", $context) ? $context["doc"] : (function () { throw new RuntimeError('Variable "doc" does not exist.', 82, $this->source); })()), "regimen", [], "any", false, false, false, 82), "html", null, true);
        echo " </td>
                        <td width=\"40%\" align=\"left\"><strong>Tasa: </strong>  ";
        // line 83
        echo twig_escape_filter($this->env, $this->env->getRuntime('Greenter\Report\Filter\FormatFilter')->number(twig_get_attribute($this->env, $this->source, (isset($context["doc"]) || array_key_exists("doc", $context) ? $context["doc"] : (function () { throw new RuntimeError('Variable "doc" does not exist.', 83, $this->source); })()), "tasa", [], "any", false, false, false, 83)), "html", null, true);
        echo "% </td>
                    </tr>
                    <tr>
                        <td width=\"60%\" align=\"left\"><strong>Tipo Moneda: </strong>  ";
        // line 86
        echo twig_escape_filter($this->env, $this->env->getRuntime('Greenter\Report\Filter\DocumentFilter')->getValueCatalog("PEN", "021"), "html", null, true);
        echo " </td>
                        <td width=\"40%\" align=\"left\"></td>
                    </tr>
                    </tbody></table>
            </div><br>
            ";
        // line 91
        $context["moneda"] = $this->env->getRuntime('Greenter\Report\Filter\DocumentFilter')->getValueCatalog("PEN", "02");
        // line 92
        echo "            <div class=\"tabla_borde\">
                <table width=\"100%\" border=\"0\" cellpadding=\"5\" cellspacing=\"0\">
                    <tbody>
                    <tr style=\"border: 2px\">
                        <td align=\"center\" class=\"bold border_right\" colspan=\"5\">Comprobante</td>
                        <td align=\"center\" class=\"bold\" colspan=\"2\">Retención</td>
                    </tr>
                    <tr class=\"border_top\">
                        <td align=\"center\" class=\"bold\">Tipo</td>
                        <td align=\"center\" class=\"bold\">Numero</td>
                        <td align=\"center\" class=\"bold\">Fecha</td>
                        <td align=\"center\" class=\"bold\">Moneda</td>
                        <td align=\"center\" class=\"bold\">Total</td>
                        <td align=\"center\" class=\"bold\">Total Retenido</td>
                        <td align=\"center\" class=\"bold\">Total Pagado</td>
                    </tr>
                        ";
        // line 108
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, (isset($context["doc"]) || array_key_exists("doc", $context) ? $context["doc"] : (function () { throw new RuntimeError('Variable "doc" does not exist.', 108, $this->source); })()), "details", [], "any", false, false, false, 108));
        foreach ($context['_seq'] as $context["_key"] => $context["det"]) {
            // line 109
            echo "                        <tr class=\"border_top\">
                            <td align=\"center\">";
            // line 110
            echo twig_escape_filter($this->env, $this->env->getRuntime('Greenter\Report\Filter\DocumentFilter')->getValueCatalog(twig_get_attribute($this->env, $this->source, $context["det"], "tipoDoc", [], "any", false, false, false, 110), "01"), "html", null, true);
            echo "</td>
                            <td align=\"center\">";
            // line 111
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["det"], "numDoc", [], "any", false, false, false, 111), "html", null, true);
            echo "</td>
                            <td align=\"center\">";
            // line 112
            echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, $context["det"], "fechaEmision", [], "any", false, false, false, 112), "d/m/Y"), "html", null, true);
            echo "</td>
                            <td align=\"center\">";
            // line 113
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["det"], "moneda", [], "any", false, false, false, 113), "html", null, true);
            echo "</td>
                            <td align=\"center\">";
            // line 114
            echo twig_escape_filter($this->env, $this->env->getRuntime('Greenter\Report\Filter\FormatFilter')->number(twig_get_attribute($this->env, $this->source, $context["det"], "impTotal", [], "any", false, false, false, 114)), "html", null, true);
            echo "</td>
                            <td align=\"center\">";
            // line 115
            echo twig_escape_filter($this->env, $this->env->getRuntime('Greenter\Report\Filter\FormatFilter')->number(twig_get_attribute($this->env, $this->source, $context["det"], "impRetenido", [], "any", false, false, false, 115)), "html", null, true);
            echo "</td>
                            <td align=\"center\">";
            // line 116
            echo twig_escape_filter($this->env, $this->env->getRuntime('Greenter\Report\Filter\FormatFilter')->number(twig_get_attribute($this->env, $this->source, $context["det"], "impPagar", [], "any", false, false, false, 116)), "html", null, true);
            echo "</td>
                        </tr>
                        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['det'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 119
        echo "                    </tbody>
                </table></div>
            <table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
                <tbody><tr>
                    <td width=\"50%\" valign=\"top\">
                        <table width=\"100%\" border=\"0\" cellpadding=\"5\" cellspacing=\"0\">
                            <tbody>
                            <tr>
                                <td colspan=\"4\">
                                ";
        // line 128
        if (twig_get_attribute($this->env, $this->source, (isset($context["doc"]) || array_key_exists("doc", $context) ? $context["doc"] : (function () { throw new RuntimeError('Variable "doc" does not exist.', 128, $this->source); })()), "observacion", [], "any", false, false, false, 128)) {
            // line 129
            echo "                                    <br><br>
                                    <span style=\"font-family:Tahoma, Geneva, sans-serif; font-size:12px\" text-align=\"center\"><strong>Observaciones</strong></span>
                                    <br>
                                    <p>";
            // line 132
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["doc"]) || array_key_exists("doc", $context) ? $context["doc"] : (function () { throw new RuntimeError('Variable "doc" does not exist.', 132, $this->source); })()), "observacion", [], "any", false, false, false, 132), "html", null, true);
            echo "</p>
                                ";
        }
        // line 134
        echo "                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                    <td width=\"50%\" valign=\"top\">
                        <br>
                        <table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"table table-valores-totales\">
                            <tbody>
                            <tr class=\"border_bottom\">
                                <td align=\"right\"><strong>Total Retenido:</strong></td>
                                <td width=\"120\" align=\"right\"><span>";
        // line 145
        echo twig_escape_filter($this->env, (isset($context["moneda"]) || array_key_exists("moneda", $context) ? $context["moneda"] : (function () { throw new RuntimeError('Variable "moneda" does not exist.', 145, $this->source); })()), "html", null, true);
        echo "  ";
        echo twig_escape_filter($this->env, $this->env->getRuntime('Greenter\Report\Filter\FormatFilter')->number(twig_get_attribute($this->env, $this->source, (isset($context["doc"]) || array_key_exists("doc", $context) ? $context["doc"] : (function () { throw new RuntimeError('Variable "doc" does not exist.', 145, $this->source); })()), "impRetenido", [], "any", false, false, false, 145)), "html", null, true);
        echo "</span></td>
                            </tr>
                            <tr class=\"border_bottom\">
                                <td align=\"right\"><strong>Total Pagado:</strong></td>
                                <td width=\"120\" align=\"right\"><span>";
        // line 149
        echo twig_escape_filter($this->env, (isset($context["moneda"]) || array_key_exists("moneda", $context) ? $context["moneda"] : (function () { throw new RuntimeError('Variable "moneda" does not exist.', 149, $this->source); })()), "html", null, true);
        echo "  ";
        echo twig_escape_filter($this->env, $this->env->getRuntime('Greenter\Report\Filter\FormatFilter')->number(twig_get_attribute($this->env, $this->source, (isset($context["doc"]) || array_key_exists("doc", $context) ? $context["doc"] : (function () { throw new RuntimeError('Variable "doc" does not exist.', 149, $this->source); })()), "impPagado", [], "any", false, false, false, 149)), "html", null, true);
        echo "</span></td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                </tbody></table>
            <br>
            <br>
            ";
        // line 158
        if ((array_key_exists("max_items", $context) && (1 === twig_compare(twig_length_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["doc"]) || array_key_exists("doc", $context) ? $context["doc"] : (function () { throw new RuntimeError('Variable "doc" does not exist.', 158, $this->source); })()), "details", [], "any", false, false, false, 158)), (isset($context["max_items"]) || array_key_exists("max_items", $context) ? $context["max_items"] : (function () { throw new RuntimeError('Variable "max_items" does not exist.', 158, $this->source); })()))))) {
            // line 159
            echo "                <div style=\"page-break-after:always;\"></div>
            ";
        }
        // line 161
        echo "            <div>
            <table>
                <tbody>
                <tr><td width=\"100%\">
                    <blockquote>
                        ";
        // line 166
        if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["params"] ?? null), "user", [], "any", false, true, false, 166), "footer", [], "any", true, true, false, 166)) {
            // line 167
            echo "                            ";
            echo twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["params"]) || array_key_exists("params", $context) ? $context["params"] : (function () { throw new RuntimeError('Variable "params" does not exist.', 167, $this->source); })()), "user", [], "any", false, false, false, 167), "footer", [], "any", false, false, false, 167);
            echo "
                        ";
        }
        // line 169
        echo "                        ";
        if ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["params"] ?? null), "system", [], "any", false, true, false, 169), "hash", [], "any", true, true, false, 169) && twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["params"]) || array_key_exists("params", $context) ? $context["params"] : (function () { throw new RuntimeError('Variable "params" does not exist.', 169, $this->source); })()), "system", [], "any", false, false, false, 169), "hash", [], "any", false, false, false, 169))) {
            // line 170
            echo "                            <strong>Resumen:</strong>   ";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["params"]) || array_key_exists("params", $context) ? $context["params"] : (function () { throw new RuntimeError('Variable "params" does not exist.', 170, $this->source); })()), "system", [], "any", false, false, false, 170), "hash", [], "any", false, false, false, 170), "html", null, true);
            echo "<br>
                        ";
        }
        // line 172
        echo "                        <span>Representación Impresa de la ";
        echo twig_escape_filter($this->env, (isset($context["name"]) || array_key_exists("name", $context) ? $context["name"] : (function () { throw new RuntimeError('Variable "name" does not exist.', 172, $this->source); })()), "html", null, true);
        echo " ELECTRÓNICA.</span>
                    </blockquote>
                    </td>
                </tr>
                </tbody></table>
            </div>
        </td>
    </tr>
    </tbody></table>
</body></html>";
    }

    public function getTemplateName()
    {
        return "retention.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  336 => 172,  330 => 170,  327 => 169,  321 => 167,  319 => 166,  312 => 161,  308 => 159,  306 => 158,  292 => 149,  283 => 145,  270 => 134,  265 => 132,  260 => 129,  258 => 128,  247 => 119,  238 => 116,  234 => 115,  230 => 114,  226 => 113,  222 => 112,  218 => 111,  214 => 110,  211 => 109,  207 => 108,  189 => 92,  187 => 91,  179 => 86,  173 => 83,  169 => 82,  161 => 79,  156 => 77,  147 => 73,  143 => 72,  139 => 70,  137 => 69,  125 => 60,  117 => 55,  109 => 50,  91 => 37,  83 => 32,  73 => 25,  62 => 17,  54 => 11,  52 => 10,  50 => 9,  45 => 6,  43 => 5,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "retention.html.twig", "C:\\xampp\\htdocs\\jvc\\sunat\\vendor\\greenter\\report\\src\\Report\\Templates\\retention.html.twig");
    }
}
