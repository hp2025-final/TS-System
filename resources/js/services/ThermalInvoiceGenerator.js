/**
 * Thermal Invoice Generator
 * Optimized for 80mm thermal printers with 70mm content width
 */

export class ThermalInvoiceGenerator {
  constructor() {
    this.companyConfig = null
    this.isLoading = false
  }

  /**
   * Fetch company configuration from API
   */
  async fetchCompanyConfig() {
    if (this.companyConfig && !this.isLoading) {
      return this.companyConfig
    }

    if (this.isLoading) {
      // Wait for existing request to complete
      while (this.isLoading) {
        await new Promise(resolve => setTimeout(resolve, 100))
      }
      return this.companyConfig
    }

    try {
      this.isLoading = true
      const response = await fetch('/api/company-config')
      const data = await response.json()
      this.companyConfig = data
      return data
    } catch (error) {
      console.error('Failed to fetch company config:', error)
      // Return fallback config
      return {
        company: {
          name: 'TASNEEM SHAMIM',
          company_phone: '+92 313 6520007',
          website: 'www.tasneemshamim.com',
          instagram: 'tasneemshamim.pk'
        },
        branch: {
          name: 'Tasneem Shamim (Gujranwala)',
          phone: '+92 313 6520007',
          address: 'G.T Road, Gujranwala'
        },
        invoice: {
          thermal_width: 70,
          font_size: 12
        }
      }
    } finally {
      this.isLoading = false
    }
  }

  /**
   * Generate and print thermal invoice
   * @param {Object} invoiceData - Complete invoice data object
   */
  async generateInvoice(invoiceData) {
    try {
      // Fetch company configuration
      const companyConfig = await this.fetchCompanyConfig()
      
      const {
        sale,
        items,
        customer,
        paymentMethod,
        subtotal,
        tax,
        total,
        totalDiscount
      } = invoiceData

      const invoiceHTML = this.buildInvoiceHTML(sale, items, customer, paymentMethod, {
        subtotal,
        tax,
        total,
        totalDiscount
      }, companyConfig)
      
      this.printInvoice(invoiceHTML)
      return true
    } catch (error) {
      console.error('Invoice generation failed:', error)
      return false
    }
  }

  /**
   * Build complete HTML for thermal invoice
   */
  buildInvoiceHTML(saleData, cartItems, customerInfo, paymentMethod, totals, companyConfig) {
    const styles = this.getThermalStyles(companyConfig)
    const header = this.buildHeader(companyConfig)
    const invoiceDetails = this.buildInvoiceDetails(saleData, customerInfo, paymentMethod)
    const itemsSection = this.buildItemsSection(cartItems)
    const totalsSection = this.buildTotalsSection(totals)
    const footer = this.buildFooter(companyConfig)

    return `<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Invoice</title>
  <style>${styles}</style>
</head>
<body>
  <div class="invoice-container">
    ${header}
    ${invoiceDetails}
    <div class="dotted-line"></div>
    ${itemsSection}
    ${totalsSection}
    ${footer}
  </div>
</body>
</html>`
  }

  /**
   * Get CSS styles optimized for thermal printing
   */
  getThermalStyles(companyConfig) {
    const width = companyConfig?.invoice?.thermal_width || 70
    const fontSize = companyConfig?.invoice?.font_size || 12
    
    return `
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    body { 
      font-family: 'Courier New', monospace; 
      font-size: ${fontSize}px; 
      line-height: 1.2;
      width: ${width}mm;
      margin: 0 auto;
      padding: 5mm;
      background: white;
    }
    
    .invoice-container {
      width: 100%;
      max-width: ${width}mm;
    }
    
    .header { 
      text-align: left; 
      border-bottom: 1px dashed #000; 
      padding-bottom: 8px; 
      margin-bottom: 8px; 
    }
    
    .company-logo {
      margin-bottom: 5px;
      text-align: left;
    }
    
    .logo-img {
      max-width: 40mm;
      height: auto;
      max-height: 15mm;
    }
    
    .branch-name {
      font-size: ${fontSize + 2}px;
      font-weight: bold;
      margin-bottom: 3px;
      text-transform: uppercase;
      text-align: left;
    }
    
    .contact-info {
      margin-bottom: 3px;
      text-align: left;
    }
    
    .contact-info div {
      margin-bottom: 1px;
    }
    
    .social-item {
      display: flex;
      align-items: center;
      gap: 2px;
      font-size: ${fontSize - 2}px;
    }
    
    .social-icon {
      width: 8px;
      height: 8px;
      fill: currentColor;
    }
    
    .company-details { 
      font-size: ${fontSize - 2}px; 
      margin-bottom: 2px; 
      line-height: 1.3;
      text-align: left;
    }
    
    .invoice-details { 
      margin-bottom: 8px; 
      font-size: ${fontSize - 1}px;
    }
    
    .invoice-title {
      text-align: center;
      font-weight: bold;
      margin-bottom: 3px;
      font-size: ${fontSize + 1}px;
    }
    
    .items-section { 
      margin-bottom: 8px; 
    }
    
    .item-row { 
      border-bottom: 1px dotted #ccc; 
      padding: 4px 0; 
      font-size: ${fontSize - 1}px;
    }
    
    .item-name { 
      font-weight: bold; 
      margin-bottom: 2px;
    }
    
    .item-details { 
      display: flex; 
      justify-content: space-between; 
      margin-bottom: 1px;
    }
    
    .price-row { 
      display: flex; 
      justify-content: space-between; 
      margin-bottom: 1px;
    }
    
    .total-section { 
      border-top: 1px dashed #000; 
      padding-top: 8px; 
      margin-top: 8px; 
    }
    
    .total-row { 
      display: flex; 
      justify-content: space-between; 
      margin-bottom: 3px;
      font-size: ${fontSize - 1}px;
    }
    
    .final-total { 
      font-weight: bold; 
      font-size: ${fontSize + 1}px; 
      border-top: 1px solid #000; 
      padding-top: 3px; 
      margin-top: 3px;
    }
    
    .footer { 
      margin-top: 12px; 
      text-align: center; 
      border-top: 1px dashed #000; 
      padding-top: 8px; 
      font-size: ${fontSize - 2}px;
    }
    
    .footer-info {
      text-align: center;
      margin-top: 8px;
    }
    
    .footer-info .website {
      margin-bottom: 2px;
      font-weight: bold;
    }
    
    .footer-info .addresses {
      text-align: left;
      font-size: ${fontSize - 2}px;
      margin-bottom: 4px;
      line-height: 1.2;
    }
    
    .footer-info .address-row {
      margin-bottom: 2px;
    }
    
    .footer-info .social-media {
      display: flex;
      justify-content: center;
      gap: 8px;
      flex-wrap: wrap;
    }
    
    .text-center { 
      text-align: center; 
    }
    
    .dotted-line { 
      border-bottom: 1px dotted #000; 
      margin: 8px 0; 
    }
    
    .small-text {
      font-size: ${fontSize - 3}px;
    }
    
    @media print {
      body { 
        width: ${width}mm; 
        margin: 0;
        padding: 2mm;
      }
      .no-print { 
        display: none; 
      }
      @page {
        size: 80mm auto;
        margin: 0;
      }
    }
    `
  }

  /**
   * Build company header section
   */
  buildHeader(companyConfig) {
    const company = companyConfig?.company || {}
    const branch = companyConfig?.branch || {}
    
    return `
    <div class="header">
      <div class="company-logo">
        <img src="/company-logo.png" alt="Company Logo" class="logo-img">
      </div>
      <div class="company-details">
        <div class="branch-name">${branch.name || 'Gujranwala Branch'}</div>
        <div class="contact-info">
          <div>Branch Phone: ${branch.phone || '+92 313 6520007'}</div>
          <div>HO Phone: ${company.company_phone || '+92 300 0000000'}</div>
          <div>Email: ${company.email || 'cs@tasneemshamim.com'}</div>
        </div>
      </div>
    </div>
    `
  }

  /**
   * Build invoice details section
   */
  buildInvoiceDetails(saleData, customerInfo, paymentMethod) {
    const currentDate = new Date()
    const dateStr = currentDate.toLocaleDateString('en-GB').replace(/\//g, '/') // DD/MM/YYYY format
    const timeStr = currentDate.toLocaleTimeString('en-GB', { hour12: false }) // 24-hour format
    const customerName = customerInfo?.name || 'Walk-in Customer'
    const phoneNumber = customerInfo?.phone || ''
    
    // Use invoice number from database if available, otherwise generate new one
    let invoiceNumber = saleData?.sale?.invoice_number || saleData?.invoice_number
    if (!invoiceNumber) {
      const year = currentDate.getFullYear().toString().slice(-2)
      const month = (currentDate.getMonth() + 1).toString().padStart(2, '0')
      const day = currentDate.getDate().toString().padStart(2, '0')
      const hour = currentDate.getHours().toString().padStart(2, '0')
      const minute = currentDate.getMinutes().toString().padStart(2, '0')
      const second = currentDate.getSeconds().toString().padStart(2, '0')
      invoiceNumber = `TS-${year}${month}${day}${hour}${minute}${second}`
    }
    
    let phoneHTML = ''
    if (phoneNumber) {
      phoneHTML = `<div>Phone: ${phoneNumber}</div>`
    }

    return `
    <div class="invoice-details">
      <div class="invoice-title">INVOICE #${invoiceNumber}</div>
      <div class="dotted-line"></div>
      <div>Date: ${dateStr} T: ${timeStr}</div>
      <div>Customer: ${customerName}</div>
      ${phoneHTML}
      <div>Payment: ${paymentMethod?.toUpperCase() || 'CASH'}</div>
    </div>
    `
  }

  /**
   * Build items section
   */
  buildItemsSection(cartItems) {
    if (!cartItems || cartItems.length === 0) {
      return '<div class="items-section">No items</div>'
    }

    let itemsHTML = '<div class="items-section">'
    
    cartItems.forEach(item => {
      const itemName = item.dress?.name || item.dress_name || 'Unknown Item'
      const collectionName = item.dress?.collection?.name || item.collection_name || 'Unknown Collection'
      const size = item.dress?.size || item.size || 'N/A'
      const sku = item.dress?.sku || item.sku || 'N/A'
      // Prioritize barcode from sale_items table, then from dress_item
      const barcode = item.barcode || item.dress_item?.barcode || item.dress?.barcode || 'N/A'
      const salePrice = parseFloat(item.dress?.sale_price || item.sale_price || 0).toFixed(2)
      const totalDiscount = parseFloat(item.total_discount || item.total_discount_amount || 0)
      const finalPrice = parseFloat(item.final_price || item.item_total || item.dress?.sale_price || 0).toFixed(2)

      itemsHTML += `
      <div class="item-row">
        <div class="item-header">
          <span class="item-name">${itemName}</span>
          <span class="item-barcode">${barcode}</span>
        </div>
        <div class="item-details">
          <span>${collectionName}</span>
          <span>Size: ${size}</span>
        </div>
        <div class="item-details">
          <span>SKU: ${sku}</span>
          <span>Qty: 1</span>
        </div>
        <div class="price-row">
          <span>Price:</span>
          <span>Rs. ${salePrice}</span>
        </div>`

      if (totalDiscount > 0) {
        itemsHTML += `
        <div class="price-row">
          <span>Discount:</span>
          <span>-Rs. ${totalDiscount.toFixed(2)}</span>
        </div>`
      }

      itemsHTML += `
        <div class="price-row">
          <span><strong>Total:</strong></span>
          <span><strong>Rs. ${finalPrice}</strong></span>
        </div>
      </div>`
    })

    itemsHTML += '</div>'
    return itemsHTML
  }

  /**
   * Build totals section
   */
  buildTotalsSection(totals) {
    const subtotal = parseFloat(totals?.subtotal || 0).toFixed(2)
    const tax = parseFloat(totals?.tax || 0).toFixed(2)
    const total = parseFloat(totals?.total || 0).toFixed(2)

    return `
    <div class="total-section">
      <div class="total-row">
        <span>Subtotal:</span>
        <span>Rs. ${subtotal}</span>
      </div>
      <div class="total-row">
        <span>GST (18%):</span>
        <span>Rs. ${tax}</span>
      </div>
      <div class="total-row final-total">
        <span>GRAND TOTAL:</span>
        <span>Rs. ${total}</span>
      </div>
    </div>
    `
  }

  /**
   * Build footer section
   */
  buildFooter(companyConfig) {
    const company = companyConfig?.company || {}
    const branch = companyConfig?.branch || {}
    
    return `
    <div class="footer">
      <div class="text-center">
        <p>Thank you for your business!</p>
        <p>Visit us again soon!</p>
      </div>
      <div class="dotted-line"></div>
      <div class="footer-info">
        <div class="website">${company.website || 'www.tasneemshamim.com'}</div>
        <div class="addresses">
          <div class="address-row">Branch: ${branch.address || 'Fazal Centre, Ground Floor, Main G.T Road, Gujranwala'}</div>
          <div class="address-row">Head Office: ${company.company_address || 'B26, Dhedhi Business Avenue, SITE, Karachi'}</div>
        </div>
        <div class="social-media">
          <span class="social-item">
            <svg class="social-icon" viewBox="0 0 24 24" fill="currentColor">
              <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
            </svg>
            ${company.facebook || 'tasneemshamim.pk'}
          </span>
          <span class="social-item">
            <svg class="social-icon" viewBox="0 0 24 24" fill="currentColor">
              <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.40z"/>
            </svg>
            @${company.instagram || 'tasneemshamim.pk'}
          </span>
        </div>
      </div>
    </div>
    `
  }

  /**
   * Open print window and print invoice
   */
  printInvoice(htmlContent) {
    // Create a new window for printing
    const printWindow = window.open('', '_blank', 'width=350,height=700,scrollbars=yes,resizable=yes')
    
    if (!printWindow) {
      throw new Error('Could not open print window. Please allow popups for this site.')
    }

    // Write content to print window
    printWindow.document.write(htmlContent)
    printWindow.document.close()

    // Wait for content to load, then print
    printWindow.onload = () => {
      setTimeout(() => {
        printWindow.focus()
        printWindow.print()
        
        // Close window after printing (optional)
        setTimeout(() => {
          printWindow.close()
        }, 1000)
      }, 500)
    }

    // Fallback if onload doesn't fire
    setTimeout(() => {
      if (printWindow && !printWindow.closed) {
        printWindow.focus()
        printWindow.print()
      }
    }, 1000)
  }
}

// Create and export a singleton instance
export const thermalInvoice = new ThermalInvoiceGenerator()
