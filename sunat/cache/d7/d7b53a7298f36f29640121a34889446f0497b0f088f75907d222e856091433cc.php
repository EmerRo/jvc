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

/* retention.xml.twig */
class __TwigTemplate_77cecae4175287b8bc1b0f96a1011d96abb4a6a206f76f22325cc6fb9585b842 extends Template
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
        ob_start(function () { return ''; });
        // line 2
        echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>
<Retention xmlns=\"urn:sunat:names:specification:ubl:peru:schema:xsd:Retention-1\" xmlns:cac=\"urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2\" xmlns:cbc=\"urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2\" xmlns:ds=\"http://www.w3.org/2000/09/xmldsig#\" xmlns:ext=\"urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2\" xmlns:sac=\"urn:sunat:names:specification:ubl:peru:schema:xsd:SunatAggregateComponents-1\">
    <ext:UBLExtensions>
        <ext:UBLExtension>
            <ext:ExtensionContent/>
        </ext:UBLExtension>
    </ext:UBLExtensions>
    <cbc:UBLVersionID>2.0</cbc:UBLVersionID>
    <cbc:CustomizationID>1.0</cbc:CustomizationID>
    ";
        // line 11
        $context["emp"] = twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "company", [], "any", false, false, false, 11);
        // line 12
        echo "    <cac:Signature>
        <cbc:ID>";
        // line 13
        echo twig_get_attribute($this->env, $this->source, ($context["emp"] ?? null), "ruc", [], "any", false, false, false, 13);
        echo "</cbc:ID>
        <cac:SignatoryParty>
            <cac:PartyIdentification>
                <cbc:ID>";
        // line 16
        echo twig_get_attribute($this->env, $this->source, ($context["emp"] ?? null), "ruc", [], "any", false, false, false, 16);
        echo "</cbc:ID>
            </cac:PartyIdentification>
            <cac:PartyName>
                <cbc:Name><![CDATA[";
        // line 19
        echo twig_get_attribute($this->env, $this->source, ($context["emp"] ?? null), "razonSocial", [], "any", false, false, false, 19);
        echo "]]></cbc:Name>
            </cac:PartyName>
        </cac:SignatoryParty>
        <cac:DigitalSignatureAttachment>
            <cac:ExternalReference>
                <cbc:URI>#GREENTER-SIGN</cbc:URI>
            </cac:ExternalReference>
        </cac:DigitalSignatureAttachment>
    </cac:Signature>
    <cbc:ID>";
        // line 28
        echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "serie", [], "any", false, false, false, 28);
        echo "-";
        echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "correlativo", [], "any", false, false, false, 28);
        echo "</cbc:ID>
    <cbc:IssueDate>";
        // line 29
        echo twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "fechaEmision", [], "any", false, false, false, 29), "Y-m-d");
        echo "</cbc:IssueDate>
    <cbc:IssueTime>";
        // line 30
        echo twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "fechaEmision", [], "any", false, false, false, 30), "H:i:s");
        echo "</cbc:IssueTime>
    <cac:AgentParty>
        <cac:PartyIdentification>
            <cbc:ID schemeID=\"6\">";
        // line 33
        echo twig_get_attribute($this->env, $this->source, ($context["emp"] ?? null), "ruc", [], "any", false, false, false, 33);
        echo "</cbc:ID>
        </cac:PartyIdentification>
        <cac:PartyName>
            <cbc:Name><![CDATA[";
        // line 36
        echo twig_get_attribute($this->env, $this->source, ($context["emp"] ?? null), "nombreComercial", [], "any", false, false, false, 36);
        echo "]]></cbc:Name>
        </cac:PartyName>
        ";
        // line 38
        $context["addr"] = twig_get_attribute($this->env, $this->source, ($context["emp"] ?? null), "address", [], "any", false, false, false, 38);
        // line 39
        echo "        <cac:PostalAddress>
            <cbc:ID>";
        // line 40
        echo twig_get_attribute($this->env, $this->source, ($context["addr"] ?? null), "ubigueo", [], "any", false, false, false, 40);
        echo "</cbc:ID>
            <cbc:StreetName><![CDATA[";
        // line 41
        echo twig_get_attribute($this->env, $this->source, ($context["addr"] ?? null), "direccion", [], "any", false, false, false, 41);
        echo "]]></cbc:StreetName>
            <cbc:CityName>";
        // line 42
        echo twig_get_attribute($this->env, $this->source, ($context["addr"] ?? null), "departamento", [], "any", false, false, false, 42);
        echo "</cbc:CityName>
            <cbc:CountrySubentity>";
        // line 43
        echo twig_get_attribute($this->env, $this->source, ($context["addr"] ?? null), "provincia", [], "any", false, false, false, 43);
        echo "</cbc:CountrySubentity>
            <cbc:District>";
        // line 44
        echo twig_get_attribute($this->env, $this->source, ($context["addr"] ?? null), "distrito", [], "any", false, false, false, 44);
        echo "</cbc:District>
            <cac:Country>
                <cbc:IdentificationCode>";
        // line 46
        echo twig_get_attribute($this->env, $this->source, ($context["addr"] ?? null), "codigoPais", [], "any", false, false, false, 46);
        echo "</cbc:IdentificationCode>
            </cac:Country>
        </cac:PostalAddress>
        <cac:PartyLegalEntity>
            <cbc:RegistrationName><![CDATA[";
        // line 50
        echo twig_get_attribute($this->env, $this->source, ($context["emp"] ?? null), "razonSocial", [], "any", false, false, false, 50);
        echo "]]></cbc:RegistrationName>
        </cac:PartyLegalEntity>
    </cac:AgentParty>
    <cac:ReceiverParty>
        <cac:PartyIdentification>
            <cbc:ID schemeID=\"";
        // line 55
        echo twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "proveedor", [], "any", false, false, false, 55), "tipoDoc", [], "any", false, false, false, 55);
        echo "\">";
        echo twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "proveedor", [], "any", false, false, false, 55), "numDoc", [], "any", false, false, false, 55);
        echo "</cbc:ID>
        </cac:PartyIdentification>
        <cac:PartyLegalEntity>
            <cbc:RegistrationName><![CDATA[";
        // line 58
        echo twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "proveedor", [], "any", false, false, false, 58), "rznSocial", [], "any", false, false, false, 58);
        echo "]]></cbc:RegistrationName>
        </cac:PartyLegalEntity>
    </cac:ReceiverParty>
    <sac:SUNATRetentionSystemCode>";
        // line 61
        echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "regimen", [], "any", false, false, false, 61);
        echo "</sac:SUNATRetentionSystemCode>
    <sac:SUNATRetentionPercent>";
        // line 62
        echo call_user_func_array($this->env->getFilter('n_format')->getCallable(), [twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "tasa", [], "any", false, false, false, 62)]);
        echo "</sac:SUNATRetentionPercent>
    ";
        // line 63
        if (twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "observacion", [], "any", false, false, false, 63)) {
            // line 64
            echo "    <cbc:Note><![CDATA[";
            echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "observacion", [], "any", false, false, false, 64);
            echo "]]></cbc:Note>
    ";
        }
        // line 66
        echo "    <cbc:TotalInvoiceAmount currencyID=\"PEN\">";
        echo call_user_func_array($this->env->getFilter('n_format')->getCallable(), [twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "impRetenido", [], "any", false, false, false, 66)]);
        echo "</cbc:TotalInvoiceAmount>
    <sac:SUNATTotalPaid currencyID=\"PEN\">";
        // line 67
        echo call_user_func_array($this->env->getFilter('n_format')->getCallable(), [twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "impPagado", [], "any", false, false, false, 67)]);
        echo "</sac:SUNATTotalPaid>
    ";
        // line 68
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "details", [], "any", false, false, false, 68));
        foreach ($context['_seq'] as $context["_key"] => $context["det"]) {
            // line 69
            echo "    <sac:SUNATRetentionDocumentReference>
        <cbc:ID schemeID=\"";
            // line 70
            echo twig_get_attribute($this->env, $this->source, $context["det"], "tipoDoc", [], "any", false, false, false, 70);
            echo "\">";
            echo twig_get_attribute($this->env, $this->source, $context["det"], "numDoc", [], "any", false, false, false, 70);
            echo "</cbc:ID>
        <cbc:IssueDate>";
            // line 71
            echo twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, $context["det"], "fechaEmision", [], "any", false, false, false, 71), "Y-m-d");
            echo "</cbc:IssueDate>
        <cbc:TotalInvoiceAmount currencyID=\"";
            // line 72
            echo twig_get_attribute($this->env, $this->source, $context["det"], "moneda", [], "any", false, false, false, 72);
            echo "\">";
            echo call_user_func_array($this->env->getFilter('n_format')->getCallable(), [twig_get_attribute($this->env, $this->source, $context["det"], "impTotal", [], "any", false, false, false, 72)]);
            echo "</cbc:TotalInvoiceAmount>
        ";
            // line 73
            if (twig_get_attribute($this->env, $this->source, $context["det"], "pagos", [], "any", false, false, false, 73)) {
                // line 74
                echo "        ";
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, $context["det"], "pagos", [], "any", false, false, false, 74));
                $context['loop'] = [
                  'parent' => $context['_parent'],
                  'index0' => 0,
                  'index'  => 1,
                  'first'  => true,
                ];
                if (is_array($context['_seq']) || (is_object($context['_seq']) && $context['_seq'] instanceof \Countable)) {
                    $length = count($context['_seq']);
                    $context['loop']['revindex0'] = $length - 1;
                    $context['loop']['revindex'] = $length;
                    $context['loop']['length'] = $length;
                    $context['loop']['last'] = 1 === $length;
                }
                foreach ($context['_seq'] as $context["_key"] => $context["pay"]) {
                    // line 75
                    echo "        <cac:Payment>
            <cbc:ID>";
                    // line 76
                    echo twig_get_attribute($this->env, $this->source, $context["loop"], "index", [], "any", false, false, false, 76);
                    echo "</cbc:ID>
            <cbc:PaidAmount currencyID=\"";
                    // line 77
                    echo twig_get_attribute($this->env, $this->source, $context["pay"], "moneda", [], "any", false, false, false, 77);
                    echo "\">";
                    echo call_user_func_array($this->env->getFilter('n_format')->getCallable(), [twig_get_attribute($this->env, $this->source, $context["pay"], "importe", [], "any", false, false, false, 77)]);
                    echo "</cbc:PaidAmount>
            <cbc:PaidDate>";
                    // line 78
                    echo twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, $context["pay"], "fecha", [], "any", false, false, false, 78), "Y-m-d");
                    echo "</cbc:PaidDate>
        </cac:Payment>
        ";
                    ++$context['loop']['index0'];
                    ++$context['loop']['index'];
                    $context['loop']['first'] = false;
                    if (isset($context['loop']['length'])) {
                        --$context['loop']['revindex0'];
                        --$context['loop']['revindex'];
                        $context['loop']['last'] = 0 === $context['loop']['revindex0'];
                    }
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['pay'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 81
                echo "        ";
            }
            // line 82
            echo "        ";
            if (((twig_get_attribute($this->env, $this->source, $context["det"], "impRetenido", [], "any", false, false, false, 82) && twig_get_attribute($this->env, $this->source, $context["det"], "impPagar", [], "any", false, false, false, 82)) && twig_get_attribute($this->env, $this->source, $context["det"], "fechaRetencion", [], "any", false, false, false, 82))) {
                // line 83
                echo "        <sac:SUNATRetentionInformation>
            <sac:SUNATRetentionAmount currencyID=\"PEN\">";
                // line 84
                echo call_user_func_array($this->env->getFilter('n_format')->getCallable(), [twig_get_attribute($this->env, $this->source, $context["det"], "impRetenido", [], "any", false, false, false, 84)]);
                echo "</sac:SUNATRetentionAmount>
            <sac:SUNATRetentionDate>";
                // line 85
                echo twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, $context["det"], "fechaRetencion", [], "any", false, false, false, 85), "Y-m-d");
                echo "</sac:SUNATRetentionDate>
            <sac:SUNATNetTotalPaid currencyID=\"PEN\">";
                // line 86
                echo call_user_func_array($this->env->getFilter('n_format')->getCallable(), [twig_get_attribute($this->env, $this->source, $context["det"], "impPagar", [], "any", false, false, false, 86)]);
                echo "</sac:SUNATNetTotalPaid>
            ";
                // line 87
                if (twig_get_attribute($this->env, $this->source, $context["det"], "tipoCambio", [], "any", false, false, false, 87)) {
                    // line 88
                    echo "            <cac:ExchangeRate>
                <cbc:SourceCurrencyCode>";
                    // line 89
                    echo twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["det"], "tipoCambio", [], "any", false, false, false, 89), "monedaRef", [], "any", false, false, false, 89);
                    echo "</cbc:SourceCurrencyCode>
                <cbc:TargetCurrencyCode>";
                    // line 90
                    echo twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["det"], "tipoCambio", [], "any", false, false, false, 90), "monedaObj", [], "any", false, false, false, 90);
                    echo "</cbc:TargetCurrencyCode>
                <cbc:CalculationRate>";
                    // line 91
                    echo call_user_func_array($this->env->getFilter('n_format')->getCallable(), [twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["det"], "tipoCambio", [], "any", false, false, false, 91), "factor", [], "any", false, false, false, 91), 6]);
                    echo "</cbc:CalculationRate>
                <cbc:Date>";
                    // line 92
                    echo twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["det"], "tipoCambio", [], "any", false, false, false, 92), "fecha", [], "any", false, false, false, 92), "Y-m-d");
                    echo "</cbc:Date>
            </cac:ExchangeRate>
            ";
                }
                // line 95
                echo "        </sac:SUNATRetentionInformation>
        ";
            }
            // line 97
            echo "    </sac:SUNATRetentionDocumentReference>
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['det'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 99
        echo "</Retention>
";
        $___internal_7173a62934097fad1feef67879c4bbaae8735eae60fd508463d46d08164fe589_ = ('' === $tmp = ob_get_clean()) ? '' : new Markup($tmp, $this->env->getCharset());
        // line 1
        echo twig_spaceless($___internal_7173a62934097fad1feef67879c4bbaae8735eae60fd508463d46d08164fe589_);
    }

    public function getTemplateName()
    {
        return "retention.xml.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  318 => 1,  314 => 99,  307 => 97,  303 => 95,  297 => 92,  293 => 91,  289 => 90,  285 => 89,  282 => 88,  280 => 87,  276 => 86,  272 => 85,  268 => 84,  265 => 83,  262 => 82,  259 => 81,  242 => 78,  236 => 77,  232 => 76,  229 => 75,  211 => 74,  209 => 73,  203 => 72,  199 => 71,  193 => 70,  190 => 69,  186 => 68,  182 => 67,  177 => 66,  171 => 64,  169 => 63,  165 => 62,  161 => 61,  155 => 58,  147 => 55,  139 => 50,  132 => 46,  127 => 44,  123 => 43,  119 => 42,  115 => 41,  111 => 40,  108 => 39,  106 => 38,  101 => 36,  95 => 33,  89 => 30,  85 => 29,  79 => 28,  67 => 19,  61 => 16,  55 => 13,  52 => 12,  50 => 11,  39 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "retention.xml.twig", "C:\\xampp\\htdocs\\jvc\\sunat\\vendor\\greenter\\xml\\src\\Xml\\Templates\\retention.xml.twig");
    }
}
