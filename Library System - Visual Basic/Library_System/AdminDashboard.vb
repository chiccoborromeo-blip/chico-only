Imports MySql.Data.MySqlClient

Public Class frmAdmindashboard

    Private connStr As String = "server=localhost;userid=root;password=;database=library_db"
    Private currentView As String = ""
    Private isLoading As Boolean = False

    ' ═══════════════════════════════════════════════════
    ' FORM LOAD
    ' ═══════════════════════════════════════════════════
    Private Sub frmAdmindashboard_Load(sender As Object, e As EventArgs) Handles MyBase.Load
        txtSearch.Text = "🔍Search"
        txtSearch.ForeColor = Color.DarkGray
        txtSearch.Visible = False
        btnAddbooks.Visible = False
        btnAdduser.Visible = False
        LoadBookListed()
    End Sub

    ' ═══════════════════════════════════════════════════
    ' GENERIC GRID LOADER
    ' ═══════════════════════════════════════════════════
    Private Sub LoadToGrid(query As String)
        Try
            dgvLoadbooks.DataSource = Nothing
            dgvLoadbooks.Columns.Clear()
            dgvLoadbooks.AutoGenerateColumns = True

            Using conn As New MySqlConnection(connStr)
                conn.Open()
                Using adapter As New MySqlDataAdapter(query, conn)
                    Dim dt As New DataTable()
                    adapter.Fill(dt)
                    dgvLoadbooks.DataSource = dt
                End Using
            End Using

            If dgvLoadbooks.Columns.Contains("ID") Then dgvLoadbooks.Columns("ID").Visible = False
            If dgvLoadbooks.Columns.Contains("id") Then dgvLoadbooks.Columns("id").Visible = False
            dgvLoadbooks.AutoResizeColumns(DataGridViewAutoSizeColumnsMode.AllCells)

        Catch ex As MySqlException
            MessageBox.Show("Database error: " & ex.Message, "Error", MessageBoxButtons.OK, MessageBoxIcon.Error)
        Catch ex As Exception
            MessageBox.Show("Error: " & ex.Message, "Error", MessageBoxButtons.OK, MessageBoxIcon.Error)
        End Try
    End Sub

    ' ═══════════════════════════════════════════════════
    ' 1. BOOK LIST
    ' ═══════════════════════════════════════════════════
    Private Sub LoadBookListed()
        currentView = "books"
        gbList.Text = "Book List"
        txtSearch.Visible = True
        btnAddbooks.Visible = True
        btnAdduser.Visible = False

        Try
            dgvLoadbooks.DataSource = Nothing
            dgvLoadbooks.Columns.Clear()
            dgvLoadbooks.AutoGenerateColumns = False

            dgvLoadbooks.Columns.Add(New DataGridViewTextBoxColumn() With {
                .Name = "colId", .DataPropertyName = "id", .Visible = False})
            dgvLoadbooks.Columns.Add(New DataGridViewTextBoxColumn() With {
                .Name = "colBookNo", .HeaderText = "Book No",
                .DataPropertyName = "book_no", .Width = 80, .ReadOnly = True})
            dgvLoadbooks.Columns.Add(New DataGridViewTextBoxColumn() With {
                .Name = "colGenre", .HeaderText = "Genre",
                .DataPropertyName = "genre", .Width = 100, .ReadOnly = True})
            dgvLoadbooks.Columns.Add(New DataGridViewTextBoxColumn() With {
                .Name = "colTitle", .HeaderText = "Title",
                .DataPropertyName = "title", .Width = 180, .ReadOnly = True})
            dgvLoadbooks.Columns.Add(New DataGridViewTextBoxColumn() With {
                .Name = "colAuthor", .HeaderText = "Author",
                .DataPropertyName = "author", .Width = 140, .ReadOnly = True})
            dgvLoadbooks.Columns.Add(New DataGridViewTextBoxColumn() With {
                .Name = "colIsbn", .HeaderText = "ISBN",
                .DataPropertyName = "isbn", .Width = 120, .ReadOnly = True})
            dgvLoadbooks.Columns.Add(New DataGridViewTextBoxColumn() With {
                .Name = "colCategory", .HeaderText = "Category",
                .DataPropertyName = "category", .Width = 100, .ReadOnly = True})
            dgvLoadbooks.Columns.Add(New DataGridViewTextBoxColumn() With {
                .Name = "colQuantity", .HeaderText = "Quantity",
                .DataPropertyName = "quantity", .Width = 70, .ReadOnly = True})
            dgvLoadbooks.Columns.Add(New DataGridViewTextBoxColumn() With {
                .Name = "colAvailable", .HeaderText = "Available",
                .DataPropertyName = "available", .Width = 70, .ReadOnly = True})
            dgvLoadbooks.Columns.Add(New DataGridViewTextBoxColumn() With {
                .Name = "colCreatedAt", .HeaderText = "Created At",
                .DataPropertyName = "created_at", .Width = 135, .ReadOnly = True})

            Dim btnDelete As New DataGridViewButtonColumn()
            btnDelete.Name = "colDelete"
            btnDelete.HeaderText = "Action"
            btnDelete.Text = "🗑 Delete"
            btnDelete.UseColumnTextForButtonValue = True
            btnDelete.Width = 90
            dgvLoadbooks.Columns.Add(btnDelete)

            Using conn As New MySqlConnection(connStr)
                conn.Open()
                Dim query As String = "
                    SELECT id, book_no, genre, title, author, isbn,
                           category, quantity, available,
                           DATE_FORMAT(created_at, '%m/%d/%Y %h:%i %p') AS created_at
                    FROM books ORDER BY created_at DESC"
                Using adapter As New MySqlDataAdapter(query, conn)
                    Dim dt As New DataTable()
                    adapter.Fill(dt)
                    dgvLoadbooks.DataSource = dt
                End Using
            End Using

        Catch ex As MySqlException
            MessageBox.Show("Database error: " & ex.Message, "Error", MessageBoxButtons.OK, MessageBoxIcon.Error)
        Catch ex As Exception
            MessageBox.Show("Error: " & ex.Message, "Error", MessageBoxButtons.OK, MessageBoxIcon.Error)
        End Try
    End Sub

    Private Sub btnBooklisted_Click(sender As Object, e As EventArgs) Handles btnBooklisted.Click
        LoadBookListed()
    End Sub

    ' ═══════════════════════════════════════════════════
    ' 2. BORROW REQUESTS
    ' ═══════════════════════════════════════════════════
    Private Sub LoadBorrowRequests()
        isLoading = True
        currentView = "borrow_requests"
        gbList.Text = "Borrow Requests"
        txtSearch.Visible = False
        btnAddbooks.Visible = False
        btnAdduser.Visible = False

        Try
            dgvLoadbooks.DataSource = Nothing
            dgvLoadbooks.Columns.Clear()
            dgvLoadbooks.AutoGenerateColumns = False

            dgvLoadbooks.Columns.Add(New DataGridViewTextBoxColumn() With {
                .Name = "colId", .DataPropertyName = "id", .Visible = False})
            dgvLoadbooks.Columns.Add(New DataGridViewTextBoxColumn() With {
                .Name = "colUserId", .DataPropertyName = "user_id", .Visible = False})
            dgvLoadbooks.Columns.Add(New DataGridViewTextBoxColumn() With {
                .Name = "colBookId", .DataPropertyName = "book_id", .Visible = False})
            dgvLoadbooks.Columns.Add(New DataGridViewTextBoxColumn() With {
                .Name = "colAdminNote", .DataPropertyName = "admin_note", .Visible = False})
            dgvLoadbooks.Columns.Add(New DataGridViewTextBoxColumn() With {
                .Name = "colBookNo", .HeaderText = "Book No",
                .DataPropertyName = "book_no", .Width = 80, .ReadOnly = True})
            dgvLoadbooks.Columns.Add(New DataGridViewTextBoxColumn() With {
                .Name = "colBorrower", .HeaderText = "Borrower",
                .DataPropertyName = "borrower", .Width = 140, .ReadOnly = True})
            dgvLoadbooks.Columns.Add(New DataGridViewTextBoxColumn() With {
                .Name = "colTitle", .HeaderText = "Book Title",
                .DataPropertyName = "title", .Width = 140, .ReadOnly = True})
            dgvLoadbooks.Columns.Add(New DataGridViewTextBoxColumn() With {
                .Name = "colPurpose", .HeaderText = "Purpose",
                .DataPropertyName = "purpose", .Width = 70, .ReadOnly = True})
            dgvLoadbooks.Columns.Add(New DataGridViewTextBoxColumn() With {
                .Name = "colBorrowDate", .HeaderText = "Borrow Date",
                .DataPropertyName = "borrow_date", .Width = 95, .ReadOnly = True})
            dgvLoadbooks.Columns.Add(New DataGridViewTextBoxColumn() With {
                .Name = "colReturnDate", .HeaderText = "Return Date",
                .DataPropertyName = "return_date", .Width = 95, .ReadOnly = True})
            dgvLoadbooks.Columns.Add(New DataGridViewTextBoxColumn() With {
                .Name = "colAgreed", .HeaderText = "Agreed",
                .DataPropertyName = "agreed", .Width = 60, .ReadOnly = True})
            dgvLoadbooks.Columns.Add(New DataGridViewTextBoxColumn() With {
                .Name = "colRequestedAt", .HeaderText = "Requested At",
                .DataPropertyName = "requested_at", .Width = 135, .ReadOnly = True})

            Dim statusDt As New DataTable()
            statusDt.Columns.Add("value")
            statusDt.Rows.Add("pending")
            statusDt.Rows.Add("approved")
            statusDt.Rows.Add("rejected")

            Dim statusCol As New DataGridViewComboBoxColumn()
            statusCol.Name = "colStatus"
            statusCol.HeaderText = "Status"
            statusCol.DataPropertyName = "status"
            statusCol.DataSource = statusDt
            statusCol.DisplayMember = "value"
            statusCol.ValueMember = "value"
            statusCol.Width = 115
            statusCol.FlatStyle = FlatStyle.Flat
            statusCol.DisplayStyle = DataGridViewComboBoxDisplayStyle.DropDownButton
            dgvLoadbooks.Columns.Add(statusCol)

            Dim btnView As New DataGridViewButtonColumn()
            btnView.Name = "colViewNote"
            btnView.HeaderText = "View Note"
            btnView.Text = "👁 View"
            btnView.UseColumnTextForButtonValue = True
            btnView.Width = 80
            dgvLoadbooks.Columns.Add(btnView)

            Dim btnEdit As New DataGridViewButtonColumn()
            btnEdit.Name = "colEditNote"
            btnEdit.HeaderText = "Edit Note"
            btnEdit.Text = "✏ Edit"
            btnEdit.UseColumnTextForButtonValue = True
            btnEdit.Width = 80
            dgvLoadbooks.Columns.Add(btnEdit)

            Using conn As New MySqlConnection(connStr)
                conn.Open()
                Dim query As String = "
                    SELECT
                        br.id,
                        br.user_id,
                        br.book_id,
                        br.book_no,
                        u.name                                            AS borrower,
                        b.title,
                        br.purpose,
                        DATE_FORMAT(br.borrow_date,  '%m/%d/%Y')          AS borrow_date,
                        DATE_FORMAT(br.return_date,  '%m/%d/%Y')          AS return_date,
                        CASE br.agreed WHEN 1 THEN 'Yes' ELSE 'No' END    AS agreed,
                        br.status,
                        br.admin_note,
                        DATE_FORMAT(br.requested_at, '%m/%d/%Y %h:%i %p') AS requested_at
                    FROM borrow_requests br
                    INNER JOIN users u ON u.id = br.user_id
                    INNER JOIN books b ON b.id = br.book_id
                    ORDER BY br.requested_at DESC"
                Using adapter As New MySqlDataAdapter(query, conn)
                    Dim dt As New DataTable()
                    adapter.Fill(dt)
                    dgvLoadbooks.DataSource = dt
                End Using
            End Using

            ColorBorrowRows()

        Catch ex As MySqlException
            MessageBox.Show("Database error: " & ex.Message, "Error", MessageBoxButtons.OK, MessageBoxIcon.Error)
        Catch ex As Exception
            MessageBox.Show("Error: " & ex.Message, "Error", MessageBoxButtons.OK, MessageBoxIcon.Error)
        Finally
            isLoading = False
        End Try
    End Sub

    Private Sub btnBorrowrequests_Click(sender As Object, e As EventArgs) Handles btnBorrowrequests.Click
        LoadBorrowRequests()
    End Sub

    ' ═══════════════════════════════════════════════════
    ' COMBOBOX COMMIT — ONLY ONE HANDLER
    ' ═══════════════════════════════════════════════════
    Private Sub dgvLoadbooks_CurrentCellDirtyStateChanged(sender As Object, e As EventArgs) Handles dgvLoadbooks.CurrentCellDirtyStateChanged
        If isLoading Then Return
        If currentView <> "borrow_requests" AndAlso currentView <> "return_requests" Then Return
        If dgvLoadbooks.CurrentCell Is Nothing Then Return
        If TypeOf dgvLoadbooks.CurrentCell Is DataGridViewComboBoxCell Then
            If dgvLoadbooks.IsCurrentCellDirty Then
                dgvLoadbooks.CommitEdit(DataGridViewDataErrorContexts.Commit)
            End If
        End If
    End Sub

    ' ═══════════════════════════════════════════════════
    ' STATUS CHANGED — ONLY ONE HANDLER
    ' ═══════════════════════════════════════════════════
    Private Sub dgvLoadbooks_CellValueChanged(sender As Object, e As DataGridViewCellEventArgs) Handles dgvLoadbooks.CellValueChanged
        If isLoading Then Return
        If e.RowIndex < 0 Then Return
        If dgvLoadbooks.Columns(e.ColumnIndex).Name <> "colStatus" Then Return

        Dim row As DataGridViewRow = dgvLoadbooks.Rows(e.RowIndex)
        Dim newStatus As String = row.Cells("colStatus").Value?.ToString()
        If String.IsNullOrWhiteSpace(newStatus) Then Return

        ' ── BORROW REQUESTS ──
        If currentView = "borrow_requests" Then
            Dim requestId As Integer = Convert.ToInt32(row.Cells("colId").Value)
            Dim userId As Integer = Convert.ToInt32(row.Cells("colUserId").Value)
            Dim bookId As Integer = Convert.ToInt32(row.Cells("colBookId").Value)

            Try
                Using conn As New MySqlConnection(connStr)
                    conn.Open()

                    Dim updateReq As New MySqlCommand("
                        UPDATE borrow_requests SET status = @status
                        WHERE id = @requestId", conn)
                    updateReq.Parameters.AddWithValue("@status", newStatus)
                    updateReq.Parameters.AddWithValue("@requestId", requestId)
                    updateReq.ExecuteNonQuery()

                    If newStatus = "approved" Then
                        Dim checkCmd As New MySqlCommand("
                            SELECT COUNT(*) FROM borrow_records
                            WHERE user_id = @userId AND book_id = @bookId
                            AND status = 'borrowed'", conn)
                        checkCmd.Parameters.AddWithValue("@userId", userId)
                        checkCmd.Parameters.AddWithValue("@bookId", bookId)

                        If Convert.ToInt32(checkCmd.ExecuteScalar()) = 0 Then
                            Dim insertCmd As New MySqlCommand("
                                INSERT INTO borrow_records
                                    (user_id, book_id, borrow_date, due_date, status)
                                VALUES
                                    (@userId, @bookId, CURDATE(),
                                     DATE_ADD(CURDATE(), INTERVAL 14 DAY), 'borrowed')", conn)
                            insertCmd.Parameters.AddWithValue("@userId", userId)
                            insertCmd.Parameters.AddWithValue("@bookId", bookId)
                            insertCmd.ExecuteNonQuery()

                            Dim updateBook As New MySqlCommand("
                                UPDATE books SET available = available - 1
                                WHERE id = @bookId AND available > 0", conn)
                            updateBook.Parameters.AddWithValue("@bookId", bookId)
                            updateBook.ExecuteNonQuery()
                        End If
                    End If
                End Using

                MessageBox.Show("Status updated to """ & newStatus & """.",
                                "Saved", MessageBoxButtons.OK, MessageBoxIcon.Information)
                LoadBorrowRequests()

            Catch ex As MySqlException
                MessageBox.Show("Database error: " & ex.Message, "Error",
                                MessageBoxButtons.OK, MessageBoxIcon.Error)
            End Try
        End If

        ' ── RETURN REQUESTS ──
        If currentView = "return_requests" Then
            Dim requestId As Integer = Convert.ToInt32(row.Cells("colId").Value)
            Dim borrowRecordId As Integer = Convert.ToInt32(row.Cells("colBorrowRecordId").Value)

            Try
                Using conn As New MySqlConnection(connStr)
                    conn.Open()

                    Dim updateReq As New MySqlCommand("
                        UPDATE return_requests SET status = @status
                        WHERE id = @requestId", conn)
                    updateReq.Parameters.AddWithValue("@status", newStatus)
                    updateReq.Parameters.AddWithValue("@requestId", requestId)
                    updateReq.ExecuteNonQuery()

                    If newStatus = "approved" Then
                        ' Mark borrow_record as returned
                        Dim updateRecord As New MySqlCommand("
                            UPDATE borrow_records
                            SET status = 'returned', return_date = CURDATE()
                            WHERE id = @borrowRecordId", conn)
                        updateRecord.Parameters.AddWithValue("@borrowRecordId", borrowRecordId)
                        updateRecord.ExecuteNonQuery()

                        ' Increment available
                        Dim updateBook As New MySqlCommand("
                            UPDATE books SET available = available + 1
                            WHERE id = (SELECT book_id FROM return_requests WHERE id = @requestId)", conn)
                        updateBook.Parameters.AddWithValue("@requestId", requestId)
                        updateBook.ExecuteNonQuery()
                    End If
                End Using

                MessageBox.Show("Status updated to """ & newStatus & """.",
                                "Saved", MessageBoxButtons.OK, MessageBoxIcon.Information)
                LoadReturnRequests()

            Catch ex As MySqlException
                MessageBox.Show("Database error: " & ex.Message, "Error",
                                MessageBoxButtons.OK, MessageBoxIcon.Error)
            End Try
        End If
    End Sub

    ' ═══════════════════════════════════════════════════
    ' DATA ERROR SUPPRESS
    ' ═══════════════════════════════════════════════════
    Private Sub dgvLoadbooks_DataError(sender As Object, e As DataGridViewDataErrorEventArgs) Handles dgvLoadbooks.DataError
        e.Cancel = True
    End Sub

    ' ═══════════════════════════════════════════════════
    ' COLOR ROWS BY STATUS
    ' ═══════════════════════════════════════════════════
    Private Sub ColorBorrowRows()
        For Each row As DataGridViewRow In dgvLoadbooks.Rows
            If row.IsNewRow Then Continue For
            Dim status As String = row.Cells("colStatus").Value?.ToString().ToLower()
            Select Case status
                Case "approved"
                    row.DefaultCellStyle.BackColor = Color.FromArgb(198, 239, 206)
                    row.DefaultCellStyle.ForeColor = Color.FromArgb(0, 97, 0)
                Case "rejected"
                    row.DefaultCellStyle.BackColor = Color.FromArgb(255, 199, 206)
                    row.DefaultCellStyle.ForeColor = Color.FromArgb(156, 0, 6)
                Case Else
                    row.DefaultCellStyle.BackColor = Color.White
                    row.DefaultCellStyle.ForeColor = Color.Black
            End Select
        Next
    End Sub

    ' ═══════════════════════════════════════════════════
    ' CELL CONTENT CLICK — ONE HANDLER FOR ALL VIEWS
    ' ═══════════════════════════════════════════════════
    Private Sub dgvLoadbooks_CellContentClick(sender As Object, e As DataGridViewCellEventArgs) Handles dgvLoadbooks.CellContentClick
        If e.RowIndex < 0 Then Return

        Dim row As DataGridViewRow = dgvLoadbooks.Rows(e.RowIndex)
        Dim colName As String = dgvLoadbooks.Columns(e.ColumnIndex).Name

        ' ── BORROW REQUESTS ──
        If currentView = "borrow_requests" Then
            Dim requestId As Integer = Convert.ToInt32(row.Cells("colId").Value)
            Dim borrower As String = row.Cells("colBorrower").Value?.ToString()
            Dim currentNote As String = row.Cells("colAdminNote").Value?.ToString()

            If colName = "colViewNote" Then
                If String.IsNullOrWhiteSpace(currentNote) Then
                    MessageBox.Show("No note has been added for this request yet.", "No Note",
                                    MessageBoxButtons.OK, MessageBoxIcon.Information)
                Else
                    MessageBox.Show($"Note for {borrower}:{vbNewLine}{vbNewLine}{currentNote}",
                                    "Admin Note", MessageBoxButtons.OK, MessageBoxIcon.Information)
                End If
                Return
            End If

            If colName = "colEditNote" Then
                Dim frm As New frmEditNote()
                frm.RequestId = requestId
                frm.BorrowerName = borrower
                frm.ExistingNote = currentNote
                frm.ShowDialog()
                LoadBorrowRequests()
                Return
            End If
        End If

        ' ── RETURN REQUESTS ──
        If currentView = "return_requests" Then
            Dim requestId As Integer = Convert.ToInt32(row.Cells("colId").Value)
            Dim currentNote As String = row.Cells("colAdminNote").Value?.ToString()
            Dim userName As String = row.Cells("colUser").Value?.ToString()

            If colName = "colViewNote" Then
                If String.IsNullOrWhiteSpace(currentNote) Then
                    MessageBox.Show("No note has been added for this request yet.", "No Note",
                                    MessageBoxButtons.OK, MessageBoxIcon.Information)
                Else
                    MessageBox.Show($"Note for {userName}:{vbNewLine}{vbNewLine}{currentNote}",
                                    "Admin Note", MessageBoxButtons.OK, MessageBoxIcon.Information)
                End If
                Return
            End If

            If colName = "colEditNote" Then
                Dim frm As New frmEditNote()
                frm.RequestId = requestId
                frm.BorrowerName = userName
                frm.ExistingNote = currentNote
                frm.ShowDialog()
                LoadReturnRequests()
                Return
            End If
        End If

        ' ── BOOKS ──
        If currentView = "books" AndAlso colName = "colDelete" Then
            Dim bookId As Integer = Convert.ToInt32(row.Cells("colId").Value)
            Dim bookTitle As String = row.Cells("colTitle").Value.ToString()

            Dim confirm As DialogResult = MessageBox.Show(
                $"Are you sure you want to delete ""{bookTitle}""? This cannot be undone.",
                "Confirm Delete", MessageBoxButtons.YesNo, MessageBoxIcon.Warning)

            If confirm = DialogResult.Yes Then
                Try
                    Using conn As New MySqlConnection(connStr)
                        conn.Open()
                        Dim cmd As New MySqlCommand("DELETE FROM books WHERE id = @id", conn)
                        cmd.Parameters.AddWithValue("@id", bookId)
                        cmd.ExecuteNonQuery()
                    End Using
                    MessageBox.Show($"""{bookTitle}"" has been deleted.", "Deleted",
                                    MessageBoxButtons.OK, MessageBoxIcon.Information)
                    LoadBookListed()
                Catch ex As MySqlException
                    If ex.Number = 1451 Then
                        MessageBox.Show($"Cannot delete ""{bookTitle}"" because it has existing borrow/return records.",
                                        "Cannot Delete", MessageBoxButtons.OK, MessageBoxIcon.Warning)
                    Else
                        MessageBox.Show("Database error: " & ex.Message, "Error",
                                        MessageBoxButtons.OK, MessageBoxIcon.Error)
                    End If
                End Try
            End If
        End If

        ' ── USERS ──
        If currentView = "users" AndAlso colName = "colDelete" Then
            Dim userId As Integer = Convert.ToInt32(row.Cells("colId").Value)
            Dim userName As String = row.Cells("colName").Value.ToString()

            Dim confirm As DialogResult = MessageBox.Show(
                $"Are you sure you want to delete ""{userName}""? This cannot be undone.",
                "Confirm Delete", MessageBoxButtons.YesNo, MessageBoxIcon.Warning)

            If confirm = DialogResult.Yes Then
                Try
                    Using conn As New MySqlConnection(connStr)
                        conn.Open()
                        Dim cmd As New MySqlCommand("DELETE FROM users WHERE id = @id", conn)
                        cmd.Parameters.AddWithValue("@id", userId)
                        cmd.ExecuteNonQuery()
                    End Using
                    MessageBox.Show($"""{userName}"" has been deleted.", "Deleted",
                                    MessageBoxButtons.OK, MessageBoxIcon.Information)
                    LoadRegisteredUsers()
                Catch ex As MySqlException
                    If ex.Number = 1451 Then
                        MessageBox.Show($"Cannot delete ""{userName}"" because they have existing borrow/return records.",
                                        "Cannot Delete", MessageBoxButtons.OK, MessageBoxIcon.Warning)
                    Else
                        MessageBox.Show("Database error: " & ex.Message, "Error",
                                        MessageBoxButtons.OK, MessageBoxIcon.Error)
                    End If
                End Try
            End If
        End If
    End Sub

    ' ═══════════════════════════════════════════════════
    ' 3. REGISTERED USERS
    ' ═══════════════════════════════════════════════════
    Private Sub LoadRegisteredUsers()
        currentView = "users"
        gbList.Text = "Registered Users"
        txtSearch.Visible = False
        btnAddbooks.Visible = False
        btnAdduser.Visible = True

        Try
            dgvLoadbooks.DataSource = Nothing
            dgvLoadbooks.Columns.Clear()
            dgvLoadbooks.AutoGenerateColumns = False

            dgvLoadbooks.Columns.Add(New DataGridViewTextBoxColumn() With {
                .Name = "colId", .DataPropertyName = "id", .Visible = False})
            dgvLoadbooks.Columns.Add(New DataGridViewTextBoxColumn() With {
                .Name = "colName", .HeaderText = "Name",
                .DataPropertyName = "name", .Width = 160, .ReadOnly = True})
            dgvLoadbooks.Columns.Add(New DataGridViewTextBoxColumn() With {
                .Name = "colEmail", .HeaderText = "Email",
                .DataPropertyName = "email", .Width = 200, .ReadOnly = True})
            dgvLoadbooks.Columns.Add(New DataGridViewTextBoxColumn() With {
                .Name = "colRole", .HeaderText = "Role",
                .DataPropertyName = "role", .Width = 100, .ReadOnly = True})
            dgvLoadbooks.Columns.Add(New DataGridViewTextBoxColumn() With {
                .Name = "colCreatedAt", .HeaderText = "Registered At",
                .DataPropertyName = "created_at", .Width = 135, .ReadOnly = True})

            Dim btnDelete As New DataGridViewButtonColumn()
            btnDelete.Name = "colDelete"
            btnDelete.HeaderText = "Action"
            btnDelete.Text = "🗑 Delete"
            btnDelete.UseColumnTextForButtonValue = True
            btnDelete.Width = 90
            dgvLoadbooks.Columns.Add(btnDelete)

            Using conn As New MySqlConnection(connStr)
                conn.Open()
                Dim query As String = "
                    SELECT id, name, email, role,
                           DATE_FORMAT(created_at, '%m/%d/%Y %h:%i %p') AS created_at
                    FROM users ORDER BY created_at DESC"
                Using adapter As New MySqlDataAdapter(query, conn)
                    Dim dt As New DataTable()
                    adapter.Fill(dt)
                    dgvLoadbooks.DataSource = dt
                End Using
            End Using

        Catch ex As MySqlException
            MessageBox.Show("Database error: " & ex.Message, "Error", MessageBoxButtons.OK, MessageBoxIcon.Error)
        Catch ex As Exception
            MessageBox.Show("Error: " & ex.Message, "Error", MessageBoxButtons.OK, MessageBoxIcon.Error)
        End Try
    End Sub

    Private Sub btnRegistereduser_Click(sender As Object, e As EventArgs) Handles btnRegistereduser.Click
        LoadRegisteredUsers()
    End Sub

    ' ═══════════════════════════════════════════════════
    ' 4. TIMES BOOKS RETURNED
    ' ═══════════════════════════════════════════════════
    Private Sub LoadTimesBookReturned()
        currentView = "returned"
        Dim query As String = "
            SELECT br.id, u.name AS 'User', b.title AS 'Book Title',
                   br.borrow_date AS 'Borrow Date', br.due_date AS 'Due Date',
                   br.return_date AS 'Return Date', br.status AS 'Status'
            FROM borrow_records br
            INNER JOIN users u ON u.id = br.user_id
            INNER JOIN books b ON b.id = br.book_id
            WHERE br.status = 'returned'
            ORDER BY br.return_date DESC"
        LoadToGrid(query)
    End Sub

    Private Sub btntimesbooksreturned_Click(sender As Object, e As EventArgs) Handles btntimesbooksreturned.Click
        LoadTimesBookReturned()
        gbList.Text = "Times Books Returned"
        txtSearch.Visible = False
        btnAddbooks.Visible = False
        btnAdduser.Visible = False
    End Sub

    ' ═══════════════════════════════════════════════════
    ' 5. RETURN REQUESTS
    ' ═══════════════════════════════════════════════════
    Private Sub LoadReturnRequests()
        isLoading = True
        currentView = "return_requests"
        gbList.Text = "Return Requests"
        txtSearch.Visible = False
        btnAddbooks.Visible = False
        btnAdduser.Visible = False

        Try
            dgvLoadbooks.DataSource = Nothing
            dgvLoadbooks.Columns.Clear()
            dgvLoadbooks.AutoGenerateColumns = False

            dgvLoadbooks.Columns.Add(New DataGridViewTextBoxColumn() With {
                .Name = "colId", .DataPropertyName = "id", .Visible = False})
            dgvLoadbooks.Columns.Add(New DataGridViewTextBoxColumn() With {
                .Name = "colAdminNote", .DataPropertyName = "admin_note", .Visible = False})
            dgvLoadbooks.Columns.Add(New DataGridViewTextBoxColumn() With {
                .Name = "colBorrowRecordId", .HeaderText = "Borrow Record ID",
                .DataPropertyName = "borrow_record_id", .Visible = False})

            dgvLoadbooks.Columns.Add(New DataGridViewTextBoxColumn() With {
                .Name = "colUser", .HeaderText = "User",
                .DataPropertyName = "user", .Width = 140, .ReadOnly = True})
            dgvLoadbooks.Columns.Add(New DataGridViewTextBoxColumn() With {
                .Name = "colTitle", .HeaderText = "Book Title",
                .DataPropertyName = "title", .Width = 160, .ReadOnly = True})
            dgvLoadbooks.Columns.Add(New DataGridViewTextBoxColumn() With {
                .Name = "colReason", .HeaderText = "Reason",
                .DataPropertyName = "reason", .Width = 160, .ReadOnly = True})
            dgvLoadbooks.Columns.Add(New DataGridViewTextBoxColumn() With {
                .Name = "colRequestedAt", .HeaderText = "Requested At",
                .DataPropertyName = "requested_at", .Width = 135, .ReadOnly = True})

            Dim statusDt As New DataTable()
            statusDt.Columns.Add("value")
            statusDt.Rows.Add("pending")
            statusDt.Rows.Add("approved")
            statusDt.Rows.Add("rejected")

            Dim statusCol As New DataGridViewComboBoxColumn()
            statusCol.Name = "colStatus"
            statusCol.HeaderText = "Status"
            statusCol.DataPropertyName = "status"
            statusCol.DataSource = statusDt
            statusCol.DisplayMember = "value"
            statusCol.ValueMember = "value"
            statusCol.Width = 115
            statusCol.FlatStyle = FlatStyle.Flat
            statusCol.DisplayStyle = DataGridViewComboBoxDisplayStyle.DropDownButton
            dgvLoadbooks.Columns.Add(statusCol)

            Dim btnView As New DataGridViewButtonColumn()
            btnView.Name = "colViewNote"
            btnView.HeaderText = "Admin Note"
            btnView.Text = "👁 View"
            btnView.UseColumnTextForButtonValue = True
            btnView.Width = 80
            dgvLoadbooks.Columns.Add(btnView)

            Dim btnEdit As New DataGridViewButtonColumn()
            btnEdit.Name = "colEditNote"
            btnEdit.HeaderText = "Edit Note"
            btnEdit.Text = "✏ Edit"
            btnEdit.UseColumnTextForButtonValue = True
            btnEdit.Width = 80
            dgvLoadbooks.Columns.Add(btnEdit)

            Using conn As New MySqlConnection(connStr)
                conn.Open()
                Dim query As String = "
                    SELECT
                        rr.id,
                        u.name                                             AS user,
                        b.title,
                        rr.borrow_record_id,
                        rr.reason,
                        rr.status,
                        rr.admin_note,
                        DATE_FORMAT(rr.requested_at, '%m/%d/%Y %h:%i %p') AS requested_at
                    FROM return_requests rr
                    INNER JOIN users u ON u.id = rr.user_id
                    INNER JOIN books b ON b.id = rr.book_id
                    ORDER BY rr.requested_at DESC"
                Using adapter As New MySqlDataAdapter(query, conn)
                    Dim dt As New DataTable()
                    adapter.Fill(dt)
                    dgvLoadbooks.DataSource = dt
                End Using
            End Using

        Catch ex As MySqlException
            MessageBox.Show("Database error: " & ex.Message, "Error", MessageBoxButtons.OK, MessageBoxIcon.Error)
        Catch ex As Exception
            MessageBox.Show("Error: " & ex.Message, "Error", MessageBoxButtons.OK, MessageBoxIcon.Error)
        Finally
            isLoading = False
        End Try
    End Sub

    Private Sub btnReturnrequests_Click(sender As Object, e As EventArgs) Handles btnReturnrequests.Click
        LoadReturnRequests()
    End Sub

    ' ═══════════════════════════════════════════════════
    ' SEARCH
    ' ═══════════════════════════════════════════════════
    Private Sub txtSearch_TextChanged(sender As Object, e As EventArgs) Handles txtSearch.TextChanged
        If txtSearch.Text = "🔍Search" OrElse currentView <> "books" Then Return
        Dim keyword As String = txtSearch.Text.Trim()
        Dim query As String = $"
            SELECT id, book_no, genre, title, author, isbn,
                   category, quantity, available,
                   DATE_FORMAT(created_at, '%m/%d/%Y %h:%i %p') AS created_at
            FROM books
            WHERE title LIKE '%{keyword}%'
               OR author LIKE '%{keyword}%'
               OR book_no LIKE '%{keyword}%'
            ORDER BY created_at DESC"
        LoadToGrid(query)
    End Sub

    Private Sub txtSearch_GotFocus(sender As Object, e As EventArgs) Handles txtSearch.GotFocus
        If txtSearch.Text = "🔍Search" Then
            txtSearch.Text = ""
            txtSearch.ForeColor = Color.Black
        End If
    End Sub

    Private Sub txtSearch_LostFocus(sender As Object, e As EventArgs) Handles txtSearch.LostFocus
        If txtSearch.Text = "" Then
            txtSearch.Text = "🔍Search"
            txtSearch.ForeColor = Color.DarkGray
        End If
    End Sub

    ' ═══════════════════════════════════════════════════
    ' ADD BOOK
    ' ═══════════════════════════════════════════════════
    Private Sub btnAddbooks_Click(sender As Object, e As EventArgs) Handles btnAddbooks.Click
        Dim frm As New AddBook()
        frm.ShowDialog()
        If currentView = "books" Then LoadBookListed()
    End Sub

    ' ═══════════════════════════════════════════════════
    ' ADD USER
    ' ═══════════════════════════════════════════════════
    Private Sub btnAdduser_Click(sender As Object, e As EventArgs) Handles btnAdduser.Click
        Dim frm As New frmRegister()
        frm.ShowDialog()
        If currentView = "users" Then LoadRegisteredUsers()
    End Sub

    ' ═══════════════════════════════════════════════════
    ' LOGOUT
    ' ═══════════════════════════════════════════════════
    Private Sub btnLogin_Click(sender As Object, e As EventArgs) Handles btnLogin.Click
        Dim confirm As DialogResult = MessageBox.Show(
            "Are you sure you want to logout?",
            "Logout", MessageBoxButtons.YesNo, MessageBoxIcon.Question)
        If confirm = DialogResult.Yes Then
            Dim frm As New frmLogin()
            frm.Show()
            Me.Close()
        End If
    End Sub

End Class