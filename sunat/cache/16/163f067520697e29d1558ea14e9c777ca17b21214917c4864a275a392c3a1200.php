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

/* invoice2.0.xml.twig */
class __TwigTemplate_b3f586c80c7d60b0915a518d6ecb27248d214dfe651689f46d10eb4fd0553a78 extends Template
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
<Invoice xmlns=\"urn:oasis:names:specification:ubl:schema:xsd:Invoice-2\" xmlns:cac=\"urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2\" xmlns:cbc=\"urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2\" xmlns:ds=\"http://www.w3.org/2000/09/xmldsig#\" xmlns:ext=\"urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2\" xmlns:sac=\"urn:sunat:names:specification:ubl:peru:schema:xsd:SunatAggregateComponents-1\">
    <ext:UBLExtensions>
        <ext:UBLExtension>
            <ext:ExtensionContent>
                <sac:AdditionalInformation>
                    ";
        // line 8
        if (twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "mtoDescuentos", [], "any", false, false, false, 8)) {
            // line 9
            echo "                    <sac:AdditionalMonetaryTotal>
                        <cbc:ID>2005</cbc:ID>
                        <cbc:PayableAmount currencyID=\"";
            // line 11
            echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "tipoMoneda", [], "any", false, false, false, 11);
            echo "\">";
            echo call_user_func_array($this->env->getFilter('n_format')->getCallable(), [twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "mtoDescuentos", [], "any", false, false, false, 11)]);
            echo "</cbc:PayableAmount>
                    </sac:AdditionalMonetaryTotal>
                    ";
        }
        // line 14
        echo "                    ";
        if (twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "mtoOperGravadas", [], "any", false, false, false, 14)) {
            // line 15
            echo "                    <sac:AdditionalMonetaryTotal>
                        <cbc:ID>1001</cbc:ID>
                        <cbc:PayableAmount currencyID=\"";
            // line 17
            echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "tipoMoneda", [], "any", false, false, false, 17);
            echo "\">";
            echo call_user_func_array($this->env->getFilter('n_format')->getCallable(), [twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "mtoOperGravadas", [], "any", false, false, false, 17)]);
            echo "</cbc:PayableAmount>
                    </sac:AdditionalMonetaryTotal>
                    ";
        }
        // line 20
        echo "                    <sac:AdditionalMonetaryTotal>
                        <cbc:ID>1002</cbc:ID>
                        <cbc:PayableAmount currencyID=\"";
        // line 22
        echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "tipoMoneda", [], "any", false, false, false, 22);
        echo "\">";
        echo call_user_func_array($this->env->getFilter('n_format')->getCallable(), [twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "mtoOperInafectas", [], "any", false, false, false, 22)]);
        echo "</cbc:PayableAmount>
                    </sac:AdditionalMonetaryTotal>
                    <sac:AdditionalMonetaryTotal>
                        <cbc:ID>1003</cbc:ID>
                        <cbc:PayableAmount currencyID=\"";
        // line 26
        echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "tipoMoneda", [], "any", false, false, false, 26);
        echo "\">";
        echo call_user_func_array($this->env->getFilter('n_format')->getCallable(), [twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "mtoOperExoneradas", [], "any", false, false, false, 26)]);
        echo "</cbc:PayableAmount>
                    </sac:AdditionalMonetaryTotal>
                    ";
        // line 28
        if (twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "mtoOperGratuitas", [], "any", false, false, false, 28)) {
            // line 29
            echo "                    <sac:AdditionalMonetaryTotal>
                        <cbc:ID>1004</cbc:ID>
                        <cbc:PayableAmount currencyID=\"";
            // line 31
            echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "tipoMoneda", [], "any", false, false, false, 31);
            echo "\">";
            echo call_user_func_array($this->env->getFilter('n_format')->getCallable(), [twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "mtoOperGratuitas", [], "any", false, false, false, 31)]);
            echo "</cbc:PayableAmount>
                    </sac:AdditionalMonetaryTotal>
                    ";
        }
        // line 34
        echo "                    ";
        if (twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "detraccion", [], "any", false, false, false, 34)) {
            // line 35
            echo "                    ";
            $context["detr"] = twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "detraccion", [], "any", false, false, false, 35);
            // line 36
            echo "                    <sac:AdditionalMonetaryTotal>
                        <cbc:ID>2003</cbc:ID>
                        ";
            // line 38
            if (twig_get_attribute($this->env, $this->source, ($context["detr"] ?? null), "valueRef", [], "any", false, false, false, 38)) {
                // line 39
                echo "                         <cbc:ReferenceAmount currencyID=\"PEN\">";
                echo call_user_func_array($this->env->getFilter('n_format')->getCallable(), [twig_get_attribute($this->env, $this->source, ($context["detr"] ?? null), "valueRef", [], "any", false, false, false, 39)]);
                echo "</cbc:ReferenceAmount>
                        ";
            }
            // line 41
            echo "                        <cbc:PayableAmount currencyID=\"PEN\">";
            echo call_user_func_array($this->env->getFilter('n_format')->getCallable(), [twig_get_attribute($this->env, $this->source, ($context["detr"] ?? null), "mount", [], "any", false, false, false, 41)]);
            echo "</cbc:PayableAmount>
                        <cbc:Percent>";
            // line 42
            echo call_user_func_array($this->env->getFilter('n_format')->getCallable(), [twig_get_attribute($this->env, $this->source, ($context["detr"] ?? null), "percent", [], "any", false, false, false, 42)]);
            echo "</cbc:Percent>
                    </sac:AdditionalMonetaryTotal>
                    ";
        }
        // line 45
        echo "                    ";
        if (twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "perception", [], "any", false, false, false, 45)) {
            // line 46
            echo "                    ";
            $context["perc"] = twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "perception", [], "any", false, false, false, 46);
            // line 47
            echo "                    <sac:AdditionalMonetaryTotal>
                        <cbc:ID schemeID=\"";
            // line 48
            echo twig_get_attribute($this->env, $this->source, ($context["perc"] ?? null), "codReg", [], "any", false, false, false, 48);
            echo "\">2001</cbc:ID>
                        <sac:ReferenceAmount currencyID=\"PEN\">";
            // line 49
            echo call_user_func_array($this->env->getFilter('n_format')->getCallable(), [twig_get_attribute($this->env, $this->source, ($context["perc"] ?? null), "mtoBase", [], "any", false, false, false, 49)]);
            echo "</sac:ReferenceAmount>
                        <cbc:PayableAmount currencyID=\"PEN\">";
            // line 50
            echo call_user_func_array($this->env->getFilter('n_format')->getCallable(), [twig_get_attribute($this->env, $this->source, ($context["perc"] ?? null), "mto", [], "any", false, false, false, 50)]);
            echo "</cbc:PayableAmount>
                        <sac:TotalAmount currencyID=\"PEN\">";
            // line 51
            echo call_user_func_array($this->env->getFilter('n_format')->getCallable(), [twig_get_attribute($this->env, $this->source, ($context["perc"] ?? null), "mtoTotal", [], "any", false, false, false, 51)]);
            echo "</sac:TotalAmount>
                    </sac:AdditionalMonetaryTotal>
                    <sac:AdditionalProperty>
                        <cbc:ID>2000</cbc:ID>
                        <cbc:Value>COMPROBANTE DE PERCEPCION</cbc:Value>
                    </sac:AdditionalProperty>
                    ";
        }
        // line 58
        echo "                    ";
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "legends", [], "any", false, false, false, 58));
        foreach ($context['_seq'] as $context["_key"] => $context["leg"]) {
            // line 59
            echo "                    <sac:AdditionalProperty>
                        <cbc:ID>";
            // line 60
            echo twig_get_attribute($this->env, $this->source, $context["leg"], "code", [], "any", false, false, false, 60);
            echo "</cbc:ID>
                        <cbc:Value>";
            // line 61
            echo twig_get_attribute($this->env, $this->source, $context["leg"], "value", [], "any", false, false, false, 61);
            echo "</cbc:Value>
                    </sac:AdditionalProperty>
                    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['leg'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 64
        echo "                    ";
        if (twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "guiaEmbebida", [], "any", false, false, false, 64)) {
            // line 65
            echo "                    ";
            $this->loadTemplate("embededDespatch.xml.twig", "invoice2.0.xml.twig", 65)->display(twig_to_array(["desp" => twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "guiaEmbebida", [], "any", false, false, false, 65)]));
            // line 66
            echo "                    ";
        }
        // line 67
        echo "                    ";
        if (twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "tipoOperacion", [], "any", false, false, false, 67)) {
            // line 68
            echo "                    <sac:SUNATTransaction>
                        <cbc:ID>";
            // line 69
            echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "tipoOperacion", [], "any", false, false, false, 69);
            echo "</cbc:ID>
                    </sac:SUNATTransaction>
                    ";
        }
        // line 72
        echo "                </sac:AdditionalInformation>
            </ext:ExtensionContent>
        </ext:UBLExtension>
        <ext:UBLExtension>
            <ext:ExtensionContent/>
        </ext:UBLExtension>
    </ext:UBLExtensions>
    <cbc:UBLVersionID>2.0</cbc:UBLVersionID>
    <cbc:CustomizationID>1.0</cbc:CustomizationID>
    <cbc:ID>";
        // line 81
        echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "serie", [], "any", false, false, false, 81);
        echo "-";
        echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "correlativo", [], "any", false, false, false, 81);
        echo "</cbc:ID>
    <cbc:IssueDate>";
        // line 82
        echo twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "fechaEmision", [], "any", false, false, false, 82), "Y-m-d");
        echo "</cbc:IssueDate>
    <cbc:IssueTime>";
        // line 83
        echo twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "fechaEmision", [], "any", false, false, false, 83), "H:i:s");
        echo "</cbc:IssueTime>
    <cbc:InvoiceTypeCode>";
        // line 84
        echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "tipoDoc", [], "any", false, false, false, 84);
        echo "</cbc:InvoiceTypeCode>
    <cbc:DocumentCurrencyCode>";
        // line 85
        echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "tipoMoneda", [], "any", false, false, false, 85);
        echo "</cbc:DocumentCurrencyCode>
    ";
        // line 86
        if (twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "compra", [], "any", false, false, false, 86)) {
            // line 87
            echo "    <cac:OrderReference>
        <cbc:ID>";
            // line 88
            echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "compra", [], "any", false, false, false, 88);
            echo "</cbc:ID>
    </cac:OrderReference>
    ";
        }
        // line 91
        echo "    ";
        if (twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "guias", [], "any", false, false, false, 91)) {
            // line 92
            echo "    ";
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "guias", [], "any", false, false, false, 92));
            foreach ($context['_seq'] as $context["_key"] => $context["guia"]) {
                // line 93
                echo "    <cac:DespatchDocumentReference>
        <cbc:ID>";
                // line 94
                echo twig_get_attribute($this->env, $this->source, $context["guia"], "nroDoc", [], "any", false, false, false, 94);
                echo "</cbc:ID>
        <cbc:DocumentTypeCode>";
                // line 95
                echo twig_get_attribute($this->env, $this->source, $context["guia"], "tipoDoc", [], "any", false, false, false, 95);
                echo "</cbc:DocumentTypeCode>
    </cac:DespatchDocumentReference>
    ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['guia'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 98
            echo "    ";
        }
        // line 99
        echo "    ";
        if (twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "relDocs", [], "any", false, false, false, 99)) {
            // line 100
            echo "    ";
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "relDocs", [], "any", false, false, false, 100));
            foreach ($context['_seq'] as $context["_key"] => $context["rel"]) {
                // line 101
                echo "    <cac:AdditionalDocumentReference>
        <cbc:ID>";
                // line 102
                echo twig_get_attribute($this->env, $this->source, $context["rel"], "nroDoc", [], "any", false, false, false, 102);
                echo "</cbc:ID>
        <cbc:DocumentTypeCode>";
                // line 103
                echo twig_get_attribute($this->env, $this->source, $context["rel"], "tipoDoc", [], "any", false, false, false, 103);
                echo "</cbc:DocumentTypeCode>
    </cac:AdditionalDocumentReference>
    ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['rel'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 106
            echo "    ";
        }
        // line 107
        echo "    ";
        $context["emp"] = twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "company", [], "any", false, false, false, 107);
        // line 108
        echo "    <cac:Signature>
        <cbc:ID>";
        // line 109
        echo twig_get_attribute($this->env, $this->source, ($context["emp"] ?? null), "ruc", [], "any", false, false, false, 109);
        echo "</cbc:ID>
        <cac:SignatoryParty>
            <cac:PartyIdentification>
                <cbc:ID>";
        // line 112
        echo twig_get_attribute($this->env, $this->source, ($context["emp"] ?? null), "ruc", [], "any", false, false, false, 112);
        echo "</cbc:ID>
            </cac:PartyIdentification>
            <cac:PartyName>
                <cbc:Name><![CDATA[";
        // line 115
        echo twig_get_attribute($this->env, $this->source, ($context["emp"] ?? null), "razonSocial", [], "any", false, false, false, 115);
        echo "]]></cbc:Name>
            </cac:PartyName>
        </cac:SignatoryParty>
        <cac:DigitalSignatureAttachment>
            <cac:ExternalReference>
                <cbc:URI>#GREENTER-SIGN</cbc:URI>
            </cac:ExternalReference>
        </cac:DigitalSignatureAttachment>
    </cac:Signature>
    <cac:AccountingSupplierParty>
        <cbc:CustomerAssignedAccountID>";
        // line 125
        echo twig_get_attribute($this->env, $this->source, ($context["emp"] ?? null), "ruc", [], "any", false, false, false, 125);
        echo "</cbc:CustomerAssignedAccountID>
        <cbc:AdditionalAccountID>6</cbc:AdditionalAccountID>
        <cac:Party>
            <cac:PartyName>
                <cbc:Name><![CDATA[";
        // line 129
        echo twig_get_attribute($this->env, $this->source, ($context["emp"] ?? null), "nombreComercial", [], "any", false, false, false, 129);
        echo "]]></cbc:Name>
            </cac:PartyName>
            ";
        // line 131
        $context["addr"] = twig_get_attribute($this->env, $this->source, ($context["emp"] ?? null), "address", [], "any", false, false, false, 131);
        // line 132
        echo "            <cac:PostalAddress>
                <cbc:ID>";
        // line 133
        echo twig_get_attribute($this->env, $this->source, ($context["addr"] ?? null), "ubigueo", [], "any", false, false, false, 133);
        echo "</cbc:ID>
                <cbc:StreetName><![CDATA[";
        // line 134
        echo twig_get_attribute($this->env, $this->source, ($context["addr"] ?? null), "direccion", [], "any", false, false, false, 134);
        echo "]]></cbc:StreetName>
                <cbc:CityName>";
        // line 135
        echo twig_get_attribute($this->env, $this->source, ($context["addr"] ?? null), "departamento", [], "any", false, false, false, 135);
        echo "</cbc:CityName>
                <cbc:CountrySubentity>";
        // line 136
        echo twig_get_attribute($this->env, $this->source, ($context["addr"] ?? null), "provincia", [], "any", false, false, false, 136);
        echo "</cbc:CountrySubentity>
                <cbc:District>";
        // line 137
        echo twig_get_attribute($this->env, $this->source, ($context["addr"] ?? null), "distrito", [], "any", false, false, false, 137);
        echo "</cbc:District>
                <cac:Country>
                    <cbc:IdentificationCode>";
        // line 139
        echo twig_get_attribute($this->env, $this->source, ($context["addr"] ?? null), "codigoPais", [], "any", false, false, false, 139);
        echo "</cbc:IdentificationCode>
                </cac:Country>
            </cac:PostalAddress>
            <cac:PartyLegalEntity>
                <cbc:RegistrationName><![CDATA[";
        // line 143
        echo twig_get_attribute($this->env, $this->source, ($context["emp"] ?? null), "razonSocial", [], "any", false, false, false, 143);
        echo "]]></cbc:RegistrationName>
            </cac:PartyLegalEntity>
            ";
        // line 145
        if ((twig_get_attribute($this->env, $this->source, ($context["emp"] ?? null), "email", [], "any", false, false, false, 145) || twig_get_attribute($this->env, $this->source, ($context["emp"] ?? null), "telephone", [], "any", false, false, false, 145))) {
            // line 146
            echo "            <cac:Contact>
                ";
            // line 147
            if (twig_get_attribute($this->env, $this->source, ($context["emp"] ?? null), "telephone", [], "any", false, false, false, 147)) {
                // line 148
                echo "                <cbc:Telephone>";
                echo twig_get_attribute($this->env, $this->source, ($context["emp"] ?? null), "telephone", [], "any", false, false, false, 148);
                echo "</cbc:Telephone>
                ";
            }
            // line 150
            echo "                ";
            if (twig_get_attribute($this->env, $this->source, ($context["emp"] ?? null), "email", [], "any", false, false, false, 150)) {
                // line 151
                echo "                <cbc:ElectronicMail>";
                echo twig_get_attribute($this->env, $this->source, ($context["emp"] ?? null), "email", [], "any", false, false, false, 151);
                echo "</cbc:ElectronicMail>
                ";
            }
            // line 153
            echo "            </cac:Contact>
            ";
        }
        // line 155
        echo "        </cac:Party>
    </cac:AccountingSupplierParty>
    ";
        // line 157
        $context["client"] = twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "client", [], "any", false, false, false, 157);
        // line 158
        echo "    <cac:AccountingCustomerParty>
        <cbc:CustomerAssignedAccountID>";
        // line 159
        echo twig_get_attribute($this->env, $this->source, ($context["client"] ?? null), "numDoc", [], "any", false, false, false, 159);
        echo "</cbc:CustomerAssignedAccountID>
        <cbc:AdditionalAccountID>";
        // line 160
        echo twig_get_attribute($this->env, $this->source, ($context["client"] ?? null), "tipoDoc", [], "any", false, false, false, 160);
        echo "</cbc:AdditionalAccountID>
        <cac:Party>
            ";
        // line 162
        if (twig_get_attribute($this->env, $this->source, ($context["client"] ?? null), "address", [], "any", false, false, false, 162)) {
            // line 163
            echo "            ";
            $context["addr"] = twig_get_attribute($this->env, $this->source, ($context["client"] ?? null), "address", [], "any", false, false, false, 163);
            // line 164
            echo "            <cac:PostalAddress>
                ";
            // line 165
            if (twig_get_attribute($this->env, $this->source, ($context["addr"] ?? null), "ubigueo", [], "any", false, false, false, 165)) {
                // line 166
                echo "                <cbc:ID>";
                echo twig_get_attribute($this->env, $this->source, ($context["addr"] ?? null), "ubigueo", [], "any", false, false, false, 166);
                echo "</cbc:ID>
                ";
            }
            // line 168
            echo "                <cbc:StreetName><![CDATA[";
            echo twig_get_attribute($this->env, $this->source, ($context["addr"] ?? null), "direccion", [], "any", false, false, false, 168);
            echo "]]></cbc:StreetName>
                <cac:Country>
                    <cbc:IdentificationCode>";
            // line 170
            echo twig_get_attribute($this->env, $this->source, ($context["addr"] ?? null), "codigoPais", [], "any", false, false, false, 170);
            echo "</cbc:IdentificationCode>
                </cac:Country>
            </cac:PostalAddress>
            ";
        }
        // line 174
        echo "            <cac:PartyLegalEntity>
                <cbc:RegistrationName><![CDATA[";
        // line 175
        echo twig_get_attribute($this->env, $this->source, ($context["client"] ?? null), "rznSocial", [], "any", false, false, false, 175);
        echo "]]></cbc:RegistrationName>
            </cac:PartyLegalEntity>
            ";
        // line 177
        if ((twig_get_attribute($this->env, $this->source, ($context["client"] ?? null), "email", [], "any", false, false, false, 177) || twig_get_attribute($this->env, $this->source, ($context["client"] ?? null), "telephone", [], "any", false, false, false, 177))) {
            // line 178
            echo "            <cac:Contact>
                ";
            // line 179
            if (twig_get_attribute($this->env, $this->source, ($context["client"] ?? null), "telephone", [], "any", false, false, false, 179)) {
                // line 180
                echo "                <cbc:Telephone>";
                echo twig_get_attribute($this->env, $this->source, ($context["client"] ?? null), "telephone", [], "any", false, false, false, 180);
                echo "</cbc:Telephone>
                ";
            }
            // line 182
            echo "                ";
            if (twig_get_attribute($this->env, $this->source, ($context["client"] ?? null), "email", [], "any", false, false, false, 182)) {
                // line 183
                echo "                <cbc:ElectronicMail>";
                echo twig_get_attribute($this->env, $this->source, ($context["client"] ?? null), "email", [], "any", false, false, false, 183);
                echo "</cbc:ElectronicMail>
                ";
            }
            // line 185
            echo "            </cac:Contact>
            ";
        }
        // line 187
        echo "        </cac:Party>
    </cac:AccountingCustomerParty>
    ";
        // line 189
        $context["seller"] = twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "seller", [], "any", false, false, false, 189);
        // line 190
        echo "    ";
        if (($context["seller"] ?? null)) {
            // line 191
            echo "    <cac:SellerSupplierParty>
        <cbc:CustomerAssignedAccountID>";
            // line 192
            echo twig_get_attribute($this->env, $this->source, ($context["seller"] ?? null), "numDoc", [], "any", false, false, false, 192);
            echo "</cbc:CustomerAssignedAccountID>
        <cbc:AdditionalAccountID>";
            // line 193
            echo twig_get_attribute($this->env, $this->source, ($context["seller"] ?? null), "tipoDoc", [], "any", false, false, false, 193);
            echo "</cbc:AdditionalAccountID>
        <cac:Party>
            ";
            // line 195
            if (twig_get_attribute($this->env, $this->source, ($context["seller"] ?? null), "address", [], "any", false, false, false, 195)) {
                // line 196
                echo "            ";
                $context["addr"] = twig_get_attribute($this->env, $this->source, ($context["seller"] ?? null), "address", [], "any", false, false, false, 196);
                // line 197
                echo "            <cac:PostalAddress>
                ";
                // line 198
                if (twig_get_attribute($this->env, $this->source, ($context["addr"] ?? null), "ubigueo", [], "any", false, false, false, 198)) {
                    // line 199
                    echo "                <cbc:ID>";
                    echo twig_get_attribute($this->env, $this->source, ($context["addr"] ?? null), "ubigueo", [], "any", false, false, false, 199);
                    echo "</cbc:ID>
                ";
                }
                // line 201
                echo "                <cbc:StreetName><![CDATA[";
                echo twig_get_attribute($this->env, $this->source, ($context["addr"] ?? null), "direccion", [], "any", false, false, false, 201);
                echo "]]></cbc:StreetName>
                <cac:Country>
                    <cbc:IdentificationCode>";
                // line 203
                echo twig_get_attribute($this->env, $this->source, ($context["addr"] ?? null), "codigoPais", [], "any", false, false, false, 203);
                echo "</cbc:IdentificationCode>
                </cac:Country>
            </cac:PostalAddress>
            ";
            }
            // line 207
            echo "            <cac:PartyLegalEntity>
                <cbc:RegistrationName><![CDATA[";
            // line 208
            echo twig_get_attribute($this->env, $this->source, ($context["seller"] ?? null), "rznSocial", [], "any", false, false, false, 208);
            echo "]]></cbc:RegistrationName>
            </cac:PartyLegalEntity>
            ";
            // line 210
            if ((twig_get_attribute($this->env, $this->source, ($context["seller"] ?? null), "email", [], "any", false, false, false, 210) || twig_get_attribute($this->env, $this->source, ($context["seller"] ?? null), "telephone", [], "any", false, false, false, 210))) {
                // line 211
                echo "            <cac:Contact>
                ";
                // line 212
                if (twig_get_attribute($this->env, $this->source, ($context["seller"] ?? null), "telephone", [], "any", false, false, false, 212)) {
                    // line 213
                    echo "                <cbc:Telephone>";
                    echo twig_get_attribute($this->env, $this->source, ($context["seller"] ?? null), "telephone", [], "any", false, false, false, 213);
                    echo "</cbc:Telephone>
                ";
                }
                // line 215
                echo "                ";
                if (twig_get_attribute($this->env, $this->source, ($context["seller"] ?? null), "email", [], "any", false, false, false, 215)) {
                    // line 216
                    echo "                <cbc:ElectronicMail>";
                    echo twig_get_attribute($this->env, $this->source, ($context["seller"] ?? null), "email", [], "any", false, false, false, 216);
                    echo "</cbc:ElectronicMail>
                ";
                }
                // line 218
                echo "            </cac:Contact>
            ";
            }
            // line 220
            echo "        </cac:Party>
    </cac:SellerSupplierParty>
    ";
        }
        // line 223
        echo "    ";
        if (twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "fecVencimiento", [], "any", false, false, false, 223)) {
            // line 224
            echo "    <cac:PaymentMeans>
        <cbc:PaymentMeansCode>-</cbc:PaymentMeansCode>
        <cbc:PaymentDueDate>";
            // line 226
            echo twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "fecVencimiento", [], "any", false, false, false, 226), "Y-m-d");
            echo "</cbc:PaymentDueDate>
    </cac:PaymentMeans>
    ";
        }
        // line 229
        echo "    ";
        if (twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "anticipos", [], "any", false, false, false, 229)) {
            // line 230
            echo "    ";
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "anticipos", [], "any", false, false, false, 230));
            foreach ($context['_seq'] as $context["_key"] => $context["ant"]) {
                // line 231
                echo "    <cac:PrepaidPayment>
        <cbc:ID schemeID=\"";
                // line 232
                echo twig_get_attribute($this->env, $this->source, $context["ant"], "tipoDocRel", [], "any", false, false, false, 232);
                echo "\">";
                echo twig_get_attribute($this->env, $this->source, $context["ant"], "nroDocRel", [], "any", false, false, false, 232);
                echo "</cbc:ID>
        <cbc:PaidAmount currencyID=\"";
                // line 233
                echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "tipoMoneda", [], "any", false, false, false, 233);
                echo "\">";
                echo call_user_func_array($this->env->getFilter('n_format')->getCallable(), [twig_get_attribute($this->env, $this->source, $context["ant"], "total", [], "any", false, false, false, 233)]);
                echo "</cbc:PaidAmount>
        <cbc:InstructionID schemeID=\"6\">";
                // line 234
                echo twig_get_attribute($this->env, $this->source, ($context["emp"] ?? null), "ruc", [], "any", false, false, false, 234);
                echo "</cbc:InstructionID>
    </cac:PrepaidPayment>
    ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['ant'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 237
            echo "    ";
        }
        // line 238
        echo "    ";
        if (twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "mtoISC", [], "any", false, false, false, 238)) {
            // line 239
            echo "    ";
            $context["iscT"] = call_user_func_array($this->env->getFilter('n_format')->getCallable(), [twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "mtoISC", [], "any", false, false, false, 239)]);
            // line 240
            echo "    <cac:TaxTotal>
        <cbc:TaxAmount currencyID=\"";
            // line 241
            echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "tipoMoneda", [], "any", false, false, false, 241);
            echo "\">";
            echo ($context["iscT"] ?? null);
            echo "</cbc:TaxAmount>
        <cac:TaxSubtotal>
            <cbc:TaxAmount currencyID=\"";
            // line 243
            echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "tipoMoneda", [], "any", false, false, false, 243);
            echo "\">";
            echo ($context["iscT"] ?? null);
            echo "</cbc:TaxAmount>
            <cac:TaxCategory>
                <cac:TaxScheme>
                    <cbc:ID>2000</cbc:ID>
                    <cbc:Name>ISC</cbc:Name>
                    <cbc:TaxTypeCode>EXC</cbc:TaxTypeCode>
                </cac:TaxScheme>
            </cac:TaxCategory>
        </cac:TaxSubtotal>
    </cac:TaxTotal>
    ";
        }
        // line 254
        echo "    ";
        $context["igvT"] = call_user_func_array($this->env->getFilter('n_format')->getCallable(), [((twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "mtoIGV", [], "any", true, true, false, 254)) ? (_twig_default_filter(twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "mtoIGV", [], "any", false, false, false, 254), 0)) : (0))]);
        // line 255
        echo "    <cac:TaxTotal>
        <cbc:TaxAmount currencyID=\"";
        // line 256
        echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "tipoMoneda", [], "any", false, false, false, 256);
        echo "\">";
        echo ($context["igvT"] ?? null);
        echo "</cbc:TaxAmount>
        <cac:TaxSubtotal>
            <cbc:TaxAmount currencyID=\"";
        // line 258
        echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "tipoMoneda", [], "any", false, false, false, 258);
        echo "\">";
        echo ($context["igvT"] ?? null);
        echo "</cbc:TaxAmount>
            <cac:TaxCategory>
                <cac:TaxScheme>
                    <cbc:ID>1000</cbc:ID>
                    <cbc:Name>IGV</cbc:Name>
                    <cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
                </cac:TaxScheme>
            </cac:TaxCategory>
        </cac:TaxSubtotal>
    </cac:TaxTotal>
    ";
        // line 268
        if (twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "sumOtrosCargos", [], "any", false, false, false, 268)) {
            // line 269
            echo "    ";
            $context["othT"] = call_user_func_array($this->env->getFilter('n_format')->getCallable(), [twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "sumOtrosCargos", [], "any", false, false, false, 269)]);
            // line 270
            echo "    <cac:TaxTotal>
        <cbc:TaxAmount currencyID=\"";
            // line 271
            echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "tipoMoneda", [], "any", false, false, false, 271);
            echo "\">";
            echo ($context["othT"] ?? null);
            echo "</cbc:TaxAmount>
        <cac:TaxSubtotal>
            <cbc:TaxAmount currencyID=\"";
            // line 273
            echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "tipoMoneda", [], "any", false, false, false, 273);
            echo "\">";
            echo ($context["othT"] ?? null);
            echo "</cbc:TaxAmount>
            <cac:TaxCategory>
                <cac:TaxScheme>
                    <cbc:ID>9999</cbc:ID>
                    <cbc:Name>OTROS</cbc:Name>
                    <cbc:TaxTypeCode>OTH</cbc:TaxTypeCode>
                </cac:TaxScheme>
            </cac:TaxCategory>
        </cac:TaxSubtotal>
    </cac:TaxTotal>
    ";
        }
        // line 284
        echo "    <cac:LegalMonetaryTotal>
        <cbc:AllowanceTotalAmount currencyID=\"";
        // line 285
        echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "tipoMoneda", [], "any", false, false, false, 285);
        echo "\">";
        echo call_user_func_array($this->env->getFilter('n_format')->getCallable(), [((twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "sumDsctoGlobal", [], "any", true, true, false, 285)) ? (_twig_default_filter(twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "sumDsctoGlobal", [], "any", false, false, false, 285), 0)) : (0))]);
        echo "</cbc:AllowanceTotalAmount>
        <cbc:ChargeTotalAmount currencyID=\"";
        // line 286
        echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "tipoMoneda", [], "any", false, false, false, 286);
        echo "\">";
        echo call_user_func_array($this->env->getFilter('n_format')->getCallable(), [((twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "mtoOtrosTributos", [], "any", true, true, false, 286)) ? (_twig_default_filter(twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "mtoOtrosTributos", [], "any", false, false, false, 286), 0)) : (0))]);
        echo "</cbc:ChargeTotalAmount>
        ";
        // line 287
        if ( !(null === twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "totalAnticipos", [], "any", false, false, false, 287))) {
            echo "<cbc:PrepaidAmount currencyID=\"";
            echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "tipoMoneda", [], "any", false, false, false, 287);
            echo "\">";
            echo call_user_func_array($this->env->getFilter('n_format')->getCallable(), [twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "totalAnticipos", [], "any", false, false, false, 287)]);
            echo "</cbc:PrepaidAmount>";
        }
        // line 288
        echo "        <cbc:PayableAmount currencyID=\"";
        echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "tipoMoneda", [], "any", false, false, false, 288);
        echo "\">";
        echo call_user_func_array($this->env->getFilter('n_format')->getCallable(), [twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "mtoImpVenta", [], "any", false, false, false, 288)]);
        echo "</cbc:PayableAmount>
    </cac:LegalMonetaryTotal>
    ";
        // line 290
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "details", [], "any", false, false, false, 290));
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
        foreach ($context['_seq'] as $context["_key"] => $context["detail"]) {
            // line 291
            echo "    <cac:InvoiceLine>
        <cbc:ID>";
            // line 292
            echo twig_get_attribute($this->env, $this->source, $context["loop"], "index", [], "any", false, false, false, 292);
            echo "</cbc:ID>
        <cbc:InvoicedQuantity unitCode=\"";
            // line 293
            echo twig_get_attribute($this->env, $this->source, $context["detail"], "unidad", [], "any", false, false, false, 293);
            echo "\">";
            echo twig_get_attribute($this->env, $this->source, $context["detail"], "cantidad", [], "any", false, false, false, 293);
            echo "</cbc:InvoicedQuantity>
        <cbc:LineExtensionAmount currencyID=\"";
            // line 294
            echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "tipoMoneda", [], "any", false, false, false, 294);
            echo "\">";
            echo call_user_func_array($this->env->getFilter('n_format')->getCallable(), [twig_get_attribute($this->env, $this->source, $context["detail"], "mtoValorVenta", [], "any", false, false, false, 294)]);
            echo "</cbc:LineExtensionAmount>
        <cac:PricingReference>
            ";
            // line 296
            if (twig_get_attribute($this->env, $this->source, $context["detail"], "mtoValorGratuito", [], "any", false, false, false, 296)) {
                // line 297
                echo "            <cac:AlternativeConditionPrice>
                <cbc:PriceAmount currencyID=\"";
                // line 298
                echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "tipoMoneda", [], "any", false, false, false, 298);
                echo "\">";
                echo call_user_func_array($this->env->getFilter('n_format')->getCallable(), [twig_get_attribute($this->env, $this->source, $context["detail"], "mtoValorGratuito", [], "any", false, false, false, 298)]);
                echo "</cbc:PriceAmount>
                <cbc:PriceTypeCode>02</cbc:PriceTypeCode>
            </cac:AlternativeConditionPrice>
            ";
            } else {
                // line 302
                echo "            <cac:AlternativeConditionPrice>
                <cbc:PriceAmount currencyID=\"";
                // line 303
                echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "tipoMoneda", [], "any", false, false, false, 303);
                echo "\">";
                echo call_user_func_array($this->env->getFilter('n_format')->getCallable(), [twig_get_attribute($this->env, $this->source, $context["detail"], "mtoPrecioUnitario", [], "any", false, false, false, 303)]);
                echo "</cbc:PriceAmount>
                <cbc:PriceTypeCode>01</cbc:PriceTypeCode>
            </cac:AlternativeConditionPrice>
            ";
            }
            // line 307
            echo "        </cac:PricingReference>
        ";
            // line 308
            if (twig_get_attribute($this->env, $this->source, $context["detail"], "descuento", [], "any", false, false, false, 308)) {
                // line 309
                echo "        <cac:AllowanceCharge>
            <cbc:ChargeIndicator>false</cbc:ChargeIndicator>
            <cbc:AllowanceChargeReasonCode>00</cbc:AllowanceChargeReasonCode>
            <cbc:Amount currencyID=\"";
                // line 312
                echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "tipoMoneda", [], "any", false, false, false, 312);
                echo "\">";
                echo call_user_func_array($this->env->getFilter('n_format')->getCallable(), [twig_get_attribute($this->env, $this->source, $context["detail"], "descuento", [], "any", false, false, false, 312)]);
                echo "</cbc:Amount>
        </cac:AllowanceCharge>
        ";
            }
            // line 315
            echo "        ";
            if (twig_get_attribute($this->env, $this->source, $context["detail"], "isc", [], "any", false, false, false, 315)) {
                // line 316
                echo "        ";
                $context["isc"] = call_user_func_array($this->env->getFilter('n_format')->getCallable(), [twig_get_attribute($this->env, $this->source, $context["detail"], "isc", [], "any", false, false, false, 316)]);
                // line 317
                echo "        <cac:TaxTotal>
            <cbc:TaxAmount currencyID=\"";
                // line 318
                echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "tipoMoneda", [], "any", false, false, false, 318);
                echo "\">";
                echo ($context["isc"] ?? null);
                echo "</cbc:TaxAmount>
            <cac:TaxSubtotal>
                <cbc:TaxAmount currencyID=\"";
                // line 320
                echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "tipoMoneda", [], "any", false, false, false, 320);
                echo "\">";
                echo ($context["isc"] ?? null);
                echo "</cbc:TaxAmount>
                <cac:TaxCategory>
                    <cbc:TierRange>";
                // line 322
                echo twig_get_attribute($this->env, $this->source, $context["detail"], "tipSisIsc", [], "any", false, false, false, 322);
                echo "</cbc:TierRange>
                    <cac:TaxScheme>
                        <cbc:ID>2000</cbc:ID>
                        <cbc:Name>ISC</cbc:Name>
                        <cbc:TaxTypeCode>EXC</cbc:TaxTypeCode>
                    </cac:TaxScheme>
                </cac:TaxCategory>
            </cac:TaxSubtotal>
        </cac:TaxTotal>
        ";
            }
            // line 332
            echo "        ";
            if (twig_get_attribute($this->env, $this->source, $context["detail"], "igv", [], "any", false, false, false, 332)) {
                // line 333
                echo "        ";
                $context["igv"] = call_user_func_array($this->env->getFilter('n_format')->getCallable(), [twig_get_attribute($this->env, $this->source, $context["detail"], "igv", [], "any", false, false, false, 333)]);
                // line 334
                echo "        <cac:TaxTotal>
            <cbc:TaxAmount currencyID=\"";
                // line 335
                echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "tipoMoneda", [], "any", false, false, false, 335);
                echo "\">";
                echo ($context["igv"] ?? null);
                echo "</cbc:TaxAmount>
            <cac:TaxSubtotal>
                <cbc:TaxAmount currencyID=\"";
                // line 337
                echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "tipoMoneda", [], "any", false, false, false, 337);
                echo "\">";
                echo ($context["igv"] ?? null);
                echo "</cbc:TaxAmount>
                <cac:TaxCategory>
                    <cbc:TaxExemptionReasonCode>";
                // line 339
                echo twig_get_attribute($this->env, $this->source, $context["detail"], "tipAfeIgv", [], "any", false, false, false, 339);
                echo "</cbc:TaxExemptionReasonCode>
                    ";
                // line 340
                $context["afect"] = Greenter\Xml\Filter\TributoFunction::getByAfectacion(twig_get_attribute($this->env, $this->source, $context["detail"], "tipAfeIgv", [], "any", false, false, false, 340));
                // line 341
                echo "                    <cac:TaxScheme>
                        <cbc:ID>";
                // line 342
                echo twig_get_attribute($this->env, $this->source, ($context["afect"] ?? null), "id", [], "any", false, false, false, 342);
                echo "</cbc:ID>
                        <cbc:Name>";
                // line 343
                echo twig_get_attribute($this->env, $this->source, ($context["afect"] ?? null), "name", [], "any", false, false, false, 343);
                echo "</cbc:Name>
                        <cbc:TaxTypeCode>";
                // line 344
                echo twig_get_attribute($this->env, $this->source, ($context["afect"] ?? null), "code", [], "any", false, false, false, 344);
                echo "</cbc:TaxTypeCode>
                    </cac:TaxScheme>
                </cac:TaxCategory>
            </cac:TaxSubtotal>
        </cac:TaxTotal>
        ";
            }
            // line 350
            echo "        <cac:Item>
            <cbc:Description><![CDATA[";
            // line 351
            echo twig_get_attribute($this->env, $this->source, $context["detail"], "descripcion", [], "any", false, false, false, 351);
            echo "]]></cbc:Description>
            <cac:SellersItemIdentification>
                <cbc:ID>";
            // line 353
            echo twig_get_attribute($this->env, $this->source, $context["detail"], "codProducto", [], "any", false, false, false, 353);
            echo "</cbc:ID>
            </cac:SellersItemIdentification>
            ";
            // line 355
            if (twig_get_attribute($this->env, $this->source, $context["detail"], "codProdSunat", [], "any", false, false, false, 355)) {
                // line 356
                echo "            <cac:CommodityClassification>
                <cbc:ItemClassificationCode listID=\"UNSPSC\" listAgencyName=\"GS1 US\" listName=\"Item Classification\">";
                // line 357
                echo twig_get_attribute($this->env, $this->source, $context["detail"], "codProdSunat", [], "any", false, false, false, 357);
                echo "</cbc:ItemClassificationCode>
            </cac:CommodityClassification>
            ";
            }
            // line 360
            echo "        </cac:Item>
        <cac:Price>
            <cbc:PriceAmount currencyID=\"";
            // line 362
            echo twig_get_attribute($this->env, $this->source, ($context["doc"] ?? null), "tipoMoneda", [], "any", false, false, false, 362);
            echo "\">";
            echo call_user_func_array($this->env->getFilter('n_format')->getCallable(), [twig_get_attribute($this->env, $this->source, $context["detail"], "mtoValorUnitario", [], "any", false, false, false, 362)]);
            echo "</cbc:PriceAmount>
        </cac:Price>
    </cac:InvoiceLine>
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
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['detail'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 366
        echo "</Invoice>
";
        $___internal_0b8c20ffe187df812dafca0385c32345c40e751ec747d3895391a8e8d095ffa2_ = ('' === $tmp = ob_get_clean()) ? '' : new Markup($tmp, $this->env->getCharset());
        // line 1
        echo twig_spaceless($___internal_0b8c20ffe187df812dafca0385c32345c40e751ec747d3895391a8e8d095ffa2_);
    }

    public function getTemplateName()
    {
        return "invoice2.0.xml.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  975 => 1,  971 => 366,  951 => 362,  947 => 360,  941 => 357,  938 => 356,  936 => 355,  931 => 353,  926 => 351,  923 => 350,  914 => 344,  910 => 343,  906 => 342,  903 => 341,  901 => 340,  897 => 339,  890 => 337,  883 => 335,  880 => 334,  877 => 333,  874 => 332,  861 => 322,  854 => 320,  847 => 318,  844 => 317,  841 => 316,  838 => 315,  830 => 312,  825 => 309,  823 => 308,  820 => 307,  811 => 303,  808 => 302,  799 => 298,  796 => 297,  794 => 296,  787 => 294,  781 => 293,  777 => 292,  774 => 291,  757 => 290,  749 => 288,  741 => 287,  735 => 286,  729 => 285,  726 => 284,  710 => 273,  703 => 271,  700 => 270,  697 => 269,  695 => 268,  680 => 258,  673 => 256,  670 => 255,  667 => 254,  651 => 243,  644 => 241,  641 => 240,  638 => 239,  635 => 238,  632 => 237,  623 => 234,  617 => 233,  611 => 232,  608 => 231,  603 => 230,  600 => 229,  594 => 226,  590 => 224,  587 => 223,  582 => 220,  578 => 218,  572 => 216,  569 => 215,  563 => 213,  561 => 212,  558 => 211,  556 => 210,  551 => 208,  548 => 207,  541 => 203,  535 => 201,  529 => 199,  527 => 198,  524 => 197,  521 => 196,  519 => 195,  514 => 193,  510 => 192,  507 => 191,  504 => 190,  502 => 189,  498 => 187,  494 => 185,  488 => 183,  485 => 182,  479 => 180,  477 => 179,  474 => 178,  472 => 177,  467 => 175,  464 => 174,  457 => 170,  451 => 168,  445 => 166,  443 => 165,  440 => 164,  437 => 163,  435 => 162,  430 => 160,  426 => 159,  423 => 158,  421 => 157,  417 => 155,  413 => 153,  407 => 151,  404 => 150,  398 => 148,  396 => 147,  393 => 146,  391 => 145,  386 => 143,  379 => 139,  374 => 137,  370 => 136,  366 => 135,  362 => 134,  358 => 133,  355 => 132,  353 => 131,  348 => 129,  341 => 125,  328 => 115,  322 => 112,  316 => 109,  313 => 108,  310 => 107,  307 => 106,  298 => 103,  294 => 102,  291 => 101,  286 => 100,  283 => 99,  280 => 98,  271 => 95,  267 => 94,  264 => 93,  259 => 92,  256 => 91,  250 => 88,  247 => 87,  245 => 86,  241 => 85,  237 => 84,  233 => 83,  229 => 82,  223 => 81,  212 => 72,  206 => 69,  203 => 68,  200 => 67,  197 => 66,  194 => 65,  191 => 64,  182 => 61,  178 => 60,  175 => 59,  170 => 58,  160 => 51,  156 => 50,  152 => 49,  148 => 48,  145 => 47,  142 => 46,  139 => 45,  133 => 42,  128 => 41,  122 => 39,  120 => 38,  116 => 36,  113 => 35,  110 => 34,  102 => 31,  98 => 29,  96 => 28,  89 => 26,  80 => 22,  76 => 20,  68 => 17,  64 => 15,  61 => 14,  53 => 11,  49 => 9,  47 => 8,  39 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "invoice2.0.xml.twig", "C:\\xampp\\htdocs\\jvc\\sunat\\vendor\\greenter\\xml\\src\\Xml\\Templates\\invoice2.0.xml.twig");
    }
}
