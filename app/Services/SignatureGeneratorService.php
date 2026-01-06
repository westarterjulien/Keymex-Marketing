<?php

namespace App\Services;

use App\Models\Brand;
use App\Models\SignatureCampaign;
use App\Models\SignatureTemplate;

class SignatureGeneratorService
{
    /**
     * Generate HTML signature for a MongoDB advisor
     *
     * @param SignatureTemplate $template
     * @param array $advisor MongoDB advisor data
     * @param bool $wrapInHtmlDocument
     * @param bool $includeCampaign Whether to include active campaign banner
     * @return string
     */
    public function generateForAdvisor(SignatureTemplate $template, array $advisor, bool $wrapInHtmlDocument = true, bool $includeCampaign = true): string
    {
        $html = $template->html_content;

        // Replace contact variables from MongoDB advisor
        $html = $this->replaceContactVariables($html, $advisor);

        // Replace brand variables if brand is associated
        if ($template->brand) {
            $html = $this->replaceBrandVariables($html, $template->brand);
        }

        // Clean up empty rows/elements in the HTML
        $html = $this->cleanupEmptyElements($html);

        // Add campaign banner if active
        if ($includeCampaign) {
            $html = $this->addCampaignBanner($html, $template->brand_id);
        }

        // If wrapInHtmlDocument is true and HTML doesn't already have DOCTYPE, wrap it
        if ($wrapInHtmlDocument && stripos($html, '<!DOCTYPE') === false) {
            $html = $this->wrapInHtmlDocument($html);
        }

        return $html;
    }

    /**
     * Generate signature using default template
     */
    public function generateDefault(array $advisor, bool $wrapInHtmlDocument = true): ?string
    {
        $template = SignatureTemplate::getDefault();

        if (!$template) {
            return null;
        }

        return $this->generateForAdvisor($template, $advisor, $wrapInHtmlDocument);
    }

    /**
     * Wrap signature content in a complete HTML document
     */
    private function wrapInHtmlDocument(string $content): string
    {
        return <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signature Email</title>
    <meta name="format-detection" content="telephone=no">
    <meta name="x-apple-disable-message-reformatting">
</head>
<body style="margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif;">
{$content}
</body>
</html>
HTML;
    }

    /**
     * Replace contact-related variables from MongoDB advisor data
     *
     * MongoDB advisor structure:
     * - firstname, lastname, email, mobile_phone, phone, picture
     * - linkedin_url, facebook_url, instagram_url (social media links)
     */
    private function replaceContactVariables(string $html, array $advisor): string
    {
        $firstName = $advisor['firstname'] ?? '';
        $lastName = $advisor['lastname'] ?? '';
        $fullName = trim("$firstName $lastName");
        $displayName = $fullName ?: ($advisor['display_name'] ?? '');

        $replacements = [
            // Contact variables
            '{{contact.firstName}}' => $firstName,
            '{{contact.lastName}}' => $lastName,
            '{{contact.displayName}}' => $displayName,
            '{{contact.fullName}}' => $fullName,
            '{{contact.email}}' => $advisor['email'] ?? '',
            '{{contact.jobTitle}}' => $advisor['job_title'] ?? 'Conseiller Immobilier',
            '{{contact.phone}}' => $advisor['phone'] ?? '',
            '{{contact.mobile}}' => $advisor['mobile_phone'] ?? '',
            '{{contact.photoUrl}}' => $advisor['picture'] ?? '',

            // Social media links
            '{{contact.linkedin}}' => $advisor['linkedin_url'] ?? '',
            '{{contact.facebook}}' => $advisor['facebook_url'] ?? '',
            '{{contact.instagram}}' => $advisor['instagram_url'] ?? '',

            // Legacy user variables (for backwards compatibility)
            '{{user.firstName}}' => $firstName,
            '{{user.lastName}}' => $lastName,
            '{{user.displayName}}' => $displayName,
            '{{user.fullName}}' => $fullName,
            '{{user.email}}' => $advisor['email'] ?? '',
            '{{user.jobTitle}}' => $advisor['job_title'] ?? 'Conseiller Immobilier',
            '{{user.phone}}' => $advisor['phone'] ?? '',
            '{{user.mobile}}' => $advisor['mobile_phone'] ?? '',
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $html);
    }

    /**
     * Replace brand-related variables
     */
    private function replaceBrandVariables(string $html, Brand $brand): string
    {
        // Extract domain from website URL for organization.domain
        $website = $brand->website ?? 'https://keymex.fr';
        $domain = parse_url($website, PHP_URL_HOST) ?? 'keymex.fr';

        $replacements = [
            '{{brand.name}}' => $brand->name ?? 'KEYMEX',
            '{{brand.logoUrl}}' => $brand->logo_url ?? '',
            '{{brand.primaryColor}}' => $brand->primary_color ?? '#8B5CF6',
            '{{brand.secondaryColor}}' => $brand->secondary_color ?? '#6c757d',
            '{{brand.accentColor}}' => $brand->accent_color ?? '',
            '{{brand.website}}' => $website,
            '{{brand.phone}}' => $brand->phone ?? '',
            '{{brand.email}}' => $brand->email ?? '',
            '{{brand.address}}' => $brand->address ?? '',
            '{{brand.linkedin}}' => $brand->linkedin_url ?? '',
            '{{brand.facebook}}' => $brand->facebook_url ?? '',
            '{{brand.instagram}}' => $brand->instagram_url ?? '',
            '{{brand.office1Name}}' => 'Bureau principal',
            '{{brand.office1Address}}' => $brand->address ?? '',
            '{{brand.office2Name}}' => $brand->office2_name ?? '',
            '{{brand.office2Address}}' => $brand->office2_address ?? '',

            // Organization variables (for backwards compatibility with signature_tool)
            '{{organization.domain}}' => $domain,
            '{{organization.name}}' => $brand->name ?? 'KEYMEX',
            '{{organization.website}}' => $website,
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $html);
    }

    /**
     * Clean up empty table rows and elements that contain only empty/whitespace content
     */
    private function cleanupEmptyElements(string $html): string
    {
        // Remove table rows that only contain empty links, images, or text
        $html = preg_replace_callback('/<tr[^>]*>.*?<\/tr>/is', function ($matches) {
            $row = $matches[0];

            // Extract text content, excluding tags
            $textContent = strip_tags($row);
            // Remove common symbols/emojis used as decorations
            $textContent = preg_replace('/[📞📱✉️🌐📍]/u', '', $textContent);
            // Trim whitespace
            $textContent = trim($textContent);

            // Check if row contains empty links (href="" or href="#" or tel: with nothing after)
            $hasEmptyLinks = preg_match('/href=["\']([\s]*|#|tel:[\s]*|mailto:[\s]*)["\']]/i', $row);
            $hasEmptyImages = preg_match('/src=["\'][\s]*["\']/i', $row);

            // If row has no text content and has empty links/images, remove it
            if (empty($textContent) && ($hasEmptyLinks || $hasEmptyImages)) {
                return '';
            }

            // If row only has text content that's whitespace or symbols, check if it's a design element
            if (empty($textContent)) {
                // Don't remove rows that contain design elements like separators
                if (preg_match('/(background-color:|background:|linear-gradient|border-top:|border-bottom:|height:\s*\d+px)/i', $row)) {
                    return $row;
                }
                return '';
            }

            return $row;
        }, $html);

        // Remove divs that only contain an emoji and an empty link
        $html = preg_replace('/<div[^>]*>\s*<strong[^>]*>[📞📱✉️🌐📍]<\/strong>\s*<a[^>]*href=["\']([\s]*|#|tel:[\s]*|mailto:[\s]*)["\'][^>]*><\/a>\s*<\/div>/u', '', $html);

        // Remove empty table rows after cleanup
        $html = preg_replace('/<tr[^>]*>\s*<\/tr>/is', '', $html);

        // Remove spans/paragraphs that are completely empty or contain only whitespace
        $html = preg_replace('/<(span|p)[^>]*>\s*<\/(span|p)>/is', '', $html);

        // Clean up multiple consecutive line breaks
        $html = preg_replace('/\n{3,}/', "\n\n", $html);

        return $html;
    }

    /**
     * Add campaign banner to signature if there's an active campaign
     */
    private function addCampaignBanner(string $html, ?int $brandId): string
    {
        $campaign = SignatureCampaign::getActiveForBrand($brandId);

        if (!$campaign || empty($campaign->banner_url)) {
            return $html;
        }

        $bannerHtml = $campaign->generateBannerHtml();

        if (empty($bannerHtml)) {
            return $html;
        }

        // Add banner at the end of the signature with some spacing
        $bannerWrapper = <<<HTML
<table cellpadding="0" cellspacing="0" border="0" style="margin-top: 15px;">
    <tr>
        <td>
            {$bannerHtml}
        </td>
    </tr>
</table>
HTML;

        return $html . "\n" . $bannerWrapper;
    }
}
