/**
 * Thermal Return Slip Generator
 * Optimized for 80mm thermal printers with 70mm content width
 * Based on ThermalInvoiceGenerator service
 */

export class ThermalReturnSlipGenerator {
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
   * Generate and print thermal return slip
   * @param {Object} returnData - Complete return data object
   */
  async generateReturnSlip(returnData) {
    try {
      // Fetch company configuration
      const companyConfig = await this.fetchCompanyConfig()
      
      const returnHTML = this.buildReturnSlipHTML(returnData, companyConfig)
      
      this.printReturnSlip(returnHTML)
      return true
    } catch (error) {
      console.error('Return slip generation failed:', error)
      return false
    }
  }

  /**
   * Build complete HTML for thermal return slip
   */
  buildReturnSlipHTML(returnData, companyConfig) {
    const styles = this.getThermalStyles(companyConfig)
    const header = this.buildHeader(companyConfig)
    const returnDetails = this.buildReturnDetails(returnData)
    const itemDetails = this.buildItemDetails(returnData)
    const exchangeDetails = returnData.returnType === 'exchange' && returnData.exchangeItem ? 
      this.buildExchangeDetails(returnData) : ''
    const totalsSection = this.buildTotalsSection(returnData)
    const footer = this.buildFooter(companyConfig, returnData)

    return `<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Return Slip</title>
  <style>${styles}</style>
</head>
<body>
  <div class="return-container">
    ${header}
    ${returnDetails}
    <div class="dotted-line"></div>
    ${itemDetails}
    ${exchangeDetails}
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
      padding: 2mm;
      background: white;
    }
    
    .return-container {
      width: 100%;
    }
    
    .header {
      text-align: center;
      margin-bottom: 4mm;
    }
    
    .header h1 {
      font-size: 16px;
      font-weight: bold;
      margin: 0;
    }
    
    .header .subtitle {
      font-size: 14px;
      font-weight: bold;
      margin: 2px 0;
    }
    
    .header .branch-info {
      font-size: 10px;
      margin: 1px 0;
    }
    
    .dotted-line {
      border-top: 1px dashed #000;
      margin: 3mm 0;
    }
    
    .solid-line {
      border-top: 1px solid #000;
      margin: 2mm 0;
    }
    
    .section {
      margin: 2mm 0;
    }
    
    .section-title {
      font-size: 12px;
      font-weight: bold;
      text-transform: uppercase;
      margin-bottom: 2mm;
      text-align: center;
    }
    
    .row {
      display: flex;
      justify-content: space-between;
      margin: 1mm 0;
      line-height: 1.3;
    }
    
    .row.center {
      justify-content: center;
      text-align: center;
    }
    
    .label {
      font-weight: normal;
    }
    
    .value {
      font-weight: bold;
    }
    
    .amount {
      font-family: 'Courier New', monospace;
      font-weight: bold;
    }
    
    .total-box {
      border: 1px solid #000;
      padding: 2mm;
      margin: 3mm 0;
      text-align: center;
      font-weight: bold;
      font-size: 14px;
    }
    
    .footer {
      text-align: center;
      font-size: 10px;
      margin-top: 4mm;
    }
    
    .footer .social {
      margin: 1mm 0;
    }
    
    @media print {
      body {
        margin: 0;
        padding: 0;
      }
      
      .no-print {
        display: none;
      }
      
      @page {
        size: 80mm auto;
        margin: 0;
        padding: 0;
      }
    }
    `
  }

  /**
   * Build header section
   */
  buildHeader(companyConfig) {
    const companyName = companyConfig?.company?.name || 'TASNEEM SHAMIM'
    const branchName = companyConfig?.branch?.name || 'Tasneem Shamim (Gujranwala)'
    const branchPhone = companyConfig?.branch?.phone || '+92 313 6520007'
    const branchAddress = companyConfig?.branch?.address || 'G.T Road, Gujranwala'

    return `
    <div class="header">
      <h1>${companyName}</h1>
      <div class="subtitle">RETURN/EXCHANGE SLIP</div>
      <div class="branch-info">${branchName}</div>
      <div class="branch-info">${branchPhone}</div>
      <div class="branch-info">${branchAddress}</div>
    </div>
    `
  }

  /**
   * Build return details section
   */
  buildReturnDetails(returnData) {
    const now = new Date()
    const date = now.toLocaleDateString('en-GB')
    const time = now.toLocaleTimeString('en-GB', { hour12: false })
    
    return `
    <div class="section">
      <div class="row">
        <span class="label">Date:</span>
        <span class="value">${date}, ${time}</span>
      </div>
      <div class="row">
        <span class="label">Type:</span>
        <span class="value">${returnData.returnType === 'exchange' ? 'EXCHANGE' : 'RETURN'}</span>
      </div>
      <div class="row">
        <span class="label">Reason:</span>
        <span class="value">${returnData.returnReason || 'N/A'}</span>
      </div>
      <div class="row">
        <span class="label">Invoice:</span>
        <span class="value">${returnData.selectedItem?.invoice_number || 'N/A'}</span>
      </div>
      <div class="row">
        <span class="label">Customer:</span>
        <span class="value">Walk-in Customer</span>
      </div>
    </div>
    `
  }

  /**
   * Build returned item details section
   */
  buildItemDetails(returnData) {
    const item = returnData.selectedItem
    if (!item) return ''

    return `
    <div class="section">
      <div class="section-title">RETURNED ITEM</div>
      <div class="row">
        <span class="label">${item.dress_name}</span>
      </div>
      <div class="row">
        <span class="label">Collection:</span>
        <span class="value">${item.collection_name}</span>
      </div>
      <div class="row">
        <span class="label">Size:</span>
        <span class="value">${item.size}</span>
      </div>
      <div class="row">
        <span class="label">Barcode:</span>
        <span class="value">${item.barcode}</span>
      </div>
      <div class="row">
        <span class="label">Original Price:</span>
        <span class="amount">Rs. ${item.original_price}</span>
      </div>
      ${item.total_discount_amount > 0 ? `
      <div class="row">
        <span class="label">Discount:</span>
        <span class="amount">-Rs. ${item.total_discount_amount}</span>
      </div>
      ` : ''}
      <div class="row">
        <span class="label">GST (${item.tax_percentage}%):</span>
        <span class="amount">Rs. ${item.tax_amount}</span>
      </div>
      <div class="row">
        <span class="label">Amount Paid:</span>
        <span class="amount">Rs. ${item.item_total}</span>
      </div>
    </div>
    `
  }

  /**
   * Build exchange item details section
   */
  buildExchangeDetails(returnData) {
    const exchangeItem = returnData.exchangeItem
    if (!exchangeItem) return ''

    return `
    <div class="dotted-line"></div>
    <div class="section">
      <div class="section-title">EXCHANGE ITEM</div>
      <div class="row">
        <span class="label">${exchangeItem.dress.name}</span>
      </div>
      <div class="row">
        <span class="label">Collection:</span>
        <span class="value">${exchangeItem.dress.collection.name}</span>
      </div>
      <div class="row">
        <span class="label">Size:</span>
        <span class="value">${exchangeItem.dress.size}</span>
      </div>
      <div class="row">
        <span class="label">Barcode:</span>
        <span class="value">${exchangeItem.barcode}</span>
      </div>
      <div class="row">
        <span class="label">Price:</span>
        <span class="amount">Rs. ${exchangeItem.final_price_with_tax}</span>
      </div>
    </div>
    `
  }

  /**
   * Build totals section
   */
  buildTotalsSection(returnData) {
    if (returnData.returnType === 'exchange' && returnData.exchangeItem && returnData.selectedItem) {
      const returnAmount = parseFloat(returnData.selectedItem.item_total)
      const exchangeAmount = parseFloat(returnData.exchangeItem.final_price_with_tax)
      const difference = exchangeAmount - returnAmount
      
      return `
      <div class="solid-line"></div>
      <div class="section">
        <div class="row">
          <span class="label">Return Amount:</span>
          <span class="amount">Rs. ${returnAmount.toFixed(2)}</span>
        </div>
        <div class="row">
          <span class="label">Exchange Amount:</span>
          <span class="amount">Rs. ${exchangeAmount.toFixed(2)}</span>
        </div>
        <div class="solid-line"></div>
        <div class="total-box">
          ${difference >= 0 ? 
            `ADDITIONAL: Rs. ${Math.abs(difference).toFixed(2)}` : 
            `REFUND: Rs. ${Math.abs(difference).toFixed(2)}`
          }
        </div>
      </div>
      `
    } else {
      const refundAmount = parseFloat(returnData.selectedItem?.item_total || 0)
      
      return `
      <div class="solid-line"></div>
      <div class="total-box">
        REFUND: Rs. ${refundAmount.toFixed(2)}
      </div>
      `
    }
  }

  /**
   * Build footer section
   */
  buildFooter(companyConfig, returnData) {
    const website = companyConfig?.company?.website || 'www.tasneemshamim.com'
    const instagram = companyConfig?.company?.instagram || 'tasneemshamim.pk'
    const phone = companyConfig?.company?.company_phone || '+92 313 6520007'

    return `
    <div class="dotted-line"></div>
    <div class="footer">
      <div>Thank you for your business!</div>
      <div>Please keep this slip for records</div>
      <div class="social">📱 ${phone}</div>
      <div class="social">🌐 ${website}</div>
      <div class="social">📷 ${instagram}</div>
      ${returnData.returnNotes ? `
      <div style="margin-top: 2mm; font-size: 9px;">
        <strong>Notes:</strong> ${returnData.returnNotes}
      </div>
      ` : ''}
    </div>
    `
  }

  /**
   * Open print dialog with return slip content
   */
  printReturnSlip(htmlContent) {
    const printWindow = window.open('', '_blank', 'width=300,height=600,scrollbars=yes,resizable=yes')
    
    if (printWindow) {
      printWindow.document.write(htmlContent)
      printWindow.document.close()
      
      // Wait for content to load, then print
      printWindow.onload = () => {
        setTimeout(() => {
          printWindow.focus()
          printWindow.print()
        }, 500)
      }
      
      // Auto-close after printing (with delay to allow printing)
      printWindow.onafterprint = () => {
        setTimeout(() => {
          printWindow.close()
        }, 1000)
      }
    } else {
      console.error('Could not open print window. Please check popup blocker settings.')
      alert('Could not open print window. Please check your popup blocker settings.')
    }
  }
}

// Export singleton instance
export const thermalReturnSlip = new ThermalReturnSlipGenerator()
