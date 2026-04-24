Imports MySql.Data.MySqlClient

Public Class userdashboard
    Private connStr As String = "server=localhost;userid=root;password=;database=Library_db"
    Public loggedInUserId As Integer
    Private currentView As String = ""

    Private Sub userdashboard_Load(sender As Object, e As EventArgs) Handles MyBase.Load
        dgvLoadbooks.RowHeadersVisible = False
        LoadBooks()
    End Sub

    ' ══════════════════════════════════════════
    ' LOAD AVAILABLE BOOKS
    ' ══════════════════════════════════════════
    Private Sub LoadBooks()
        currentView = "books"
        Try
            dgvLoadbooks.DataSource = Nothing
            dgvLoadbooks.Columns.Clear()
            dgvLoadbooks.AutoGenerateColumns = False
            dgvLoadbooks.RowHeadersVisible = False

            dgvLoadbooks.Columns.Add(New DataGridViewTextBoxColumn() With {
                .Name = "colBookId", .DataPropertyName = "id", .Visible = False})
            dgvLoadbooks.Columns.Add(New DataGridViewTextBoxColumn() With {
                .Name = "colBookNo", .HeaderText = "Book No",
                .DataPropertyName = "book_no", .Width = 90, .ReadOnly = True})
            dgvLoadbooks.Columns.Add(New DataGridViewTextBoxColumn() With {
                .Name = "colGenre", .HeaderText = "Genre",
                .DataPropertyName = "genre", .Width = 100, .ReadOnly = True})
            dgvLoadbooks.Columns.Add(New DataGridViewTextBoxColumn() With {
                .Name = "colTitle", .HeaderText = "Title",
                .DataPropertyName = "title", .Width = 180, .ReadOnly = True})
            dgvLoadbooks.Columns.Add(New DataGridViewTextBoxColumn() With {
                .Name = "colAuthor", .HeaderText = "Author",
                .DataPropertyName = "author", .Width = 130, .ReadOnly = True})
            dgvLoadbooks.Columns.Add(New DataGridViewTextBoxColumn() With {
                .Name = "colCategory", .HeaderText = "Category",
                .DataPropertyName = "category", .Width = 100, .ReadOnly = True})
            dgvLoadbooks.Columns.Add(New DataGridViewTextBoxColumn() With {
                .Name = "colAvailable", .HeaderText = "Available",
                .DataPropertyName = "available", .Width = 70, .ReadOnly = True})

            Dim btnBorrow As New DataGridViewButtonColumn()
            btnBorrow.Name = "colBorrow"
            btnBorrow.HeaderText = "Action"
            btnBorrow.Text = "📖 Borrow"
            btnBorrow.UseColumnTextForButtonValue = True
            btnBorrow.Width = 90
            dgvLoadbooks.Columns.Add(btnBorrow)

            Using conn As New MySqlConnection(connStr)
                conn.Open()
                Dim query As String = "
                    SELECT id, book_no, genre, title, author, category, available
                    FROM books
                    WHERE available > 0
                    ORDER BY title ASC"
                Using adapter As New MySqlDataAdapter(query, conn)
                    Dim dt As New DataTable()
                    adapter.Fill(dt)
                    dgvLoadbooks.DataSource = dt
                End Using
            End Using

        Catch ex As Exception
            MessageBox.Show("Error loading books: " & ex.Message, "Error",
                            MessageBoxButtons.OK, MessageBoxIcon.Error)
        End Try
    End Sub

    ' ══════════════════════════════════════════
    ' LOAD BORROWED BOOKS
    ' ══════════════════════════════════════════
    Private Sub LoadBorrowedBooks()
        currentView = "borrowed"
        Try
            dgvLoadbooks.DataSource = Nothing
            dgvLoadbooks.Columns.Clear()
            dgvLoadbooks.AutoGenerateColumns = False
            dgvLoadbooks.RowHeadersVisible = False

            dgvLoadbooks.Columns.Add(New DataGridViewTextBoxColumn() With {
                .Name = "colBorrowId", .DataPropertyName = "id", .Visible = False})
            dgvLoadbooks.Columns.Add(New DataGridViewTextBoxColumn() With {
                .Name = "colBookId", .DataPropertyName = "book_id", .Visible = False})
            dgvLoadbooks.Columns.Add(New DataGridViewTextBoxColumn() With {
                .Name = "colBookNo", .HeaderText = "Book No",
                .DataPropertyName = "book_no", .Width = 90, .ReadOnly = True})
            dgvLoadbooks.Columns.Add(New DataGridViewTextBoxColumn() With {
                .Name = "colTitle", .HeaderText = "Book Title",
                .DataPropertyName = "title", .Width = 180, .ReadOnly = True})
            dgvLoadbooks.Columns.Add(New DataGridViewTextBoxColumn() With {
                .Name = "colBorrowDate", .HeaderText = "Borrow Date",
                .DataPropertyName = "borrow_date", .Width = 100, .ReadOnly = True})
            dgvLoadbooks.Columns.Add(New DataGridViewTextBoxColumn() With {
                .Name = "colDueDate", .HeaderText = "Due Date",
                .DataPropertyName = "due_date", .Width = 100, .ReadOnly = True})
            dgvLoadbooks.Columns.Add(New DataGridViewTextBoxColumn() With {
                .Name = "colStatus", .HeaderText = "Status",
                .DataPropertyName = "status", .Width = 80, .ReadOnly = True})

            Dim btnReturn As New DataGridViewButtonColumn()
            btnReturn.Name = "colReturn"
            btnReturn.HeaderText = "Action"
            btnReturn.Text = "↩ Return"
            btnReturn.UseColumnTextForButtonValue = True
            btnReturn.Width = 90
            dgvLoadbooks.Columns.Add(btnReturn)

            Using conn As New MySqlConnection(connStr)
                conn.Open()
                Dim query As String = "
                    SELECT
                        br.id,
                        br.book_id,
                        b.book_no,
                        b.title,
                        DATE_FORMAT(br.borrow_date, '%m/%d/%Y') AS borrow_date,
                        DATE_FORMAT(br.due_date,    '%m/%d/%Y') AS due_date,
                        br.status
                    FROM borrow_records br
                    INNER JOIN books b ON b.id = br.book_id
                    WHERE br.user_id = @userId
                    AND br.status = 'borrowed'
                    ORDER BY br.borrow_date DESC"
                Using adapter As New MySqlDataAdapter(query, conn)
                    adapter.SelectCommand.Parameters.AddWithValue("@userId", loggedInUserId)
                    Dim dt As New DataTable()
                    adapter.Fill(dt)
                    dgvLoadbooks.DataSource = dt
                End Using
            End Using

        Catch ex As Exception
            MessageBox.Show("Error loading borrowed books: " & ex.Message, "Error",
                            MessageBoxButtons.OK, MessageBoxIcon.Error)
        End Try
    End Sub

    ' ══════════════════════════════════════════
    ' CELL CLICK HANDLER
    ' ══════════════════════════════════════════
    Private Sub dgvLoadbooks_CellContentClick(sender As Object, e As DataGridViewCellEventArgs) Handles dgvLoadbooks.CellContentClick
        If e.RowIndex < 0 Then Return

        Dim row As DataGridViewRow = dgvLoadbooks.Rows(e.RowIndex)
        Dim colName As String = dgvLoadbooks.Columns(e.ColumnIndex).Name

        ' ══════════════════════════════
        ' BOOKS VIEW — Borrow
        ' ══════════════════════════════
        If currentView = "books" AndAlso colName = "colBorrow" Then
            Dim bookId As Integer = Convert.ToInt32(row.Cells("colBookId").Value)
            Dim bookNo As String = row.Cells("colBookNo").Value?.ToString()
            Dim bookTitle As String = row.Cells("colTitle").Value?.ToString()

            Try
                ' Check duplicate pending request
                Using conn As New MySqlConnection(connStr)
                    conn.Open()
                    Dim dupCmd As New MySqlCommand("
                        SELECT COUNT(*) FROM borrow_requests
                        WHERE user_id = @userId AND book_id = @bookId
                        AND status = 'pending'", conn)
                    dupCmd.Parameters.AddWithValue("@userId", loggedInUserId)
                    dupCmd.Parameters.AddWithValue("@bookId", bookId)

                    If Convert.ToInt32(dupCmd.ExecuteScalar()) > 0 Then
                        MessageBox.Show("You already have a pending borrow request for this book.",
                                        "Duplicate Request", MessageBoxButtons.OK, MessageBoxIcon.Warning)
                        Return
                    End If
                End Using

                Dim confirm As DialogResult = MessageBox.Show(
                    $"Request to borrow ""{bookTitle}""?",
                    "Confirm Borrow", MessageBoxButtons.YesNo, MessageBoxIcon.Question)
                If confirm <> DialogResult.Yes Then Return

                Using conn As New MySqlConnection(connStr)
                    conn.Open()
                    Dim cmd As New MySqlCommand("
                        INSERT INTO borrow_requests
                            (user_id, book_id, book_no, purpose, borrow_date, return_date, status, requested_at)
                        VALUES
                            (@userId, @bookId, @bookNo, 'Personal', CURDATE(),
                             DATE_ADD(CURDATE(), INTERVAL 14 DAY), 'pending', NOW())", conn)
                    cmd.Parameters.AddWithValue("@userId", loggedInUserId)
                    cmd.Parameters.AddWithValue("@bookId", bookId)
                    cmd.Parameters.AddWithValue("@bookNo", bookNo)
                    cmd.ExecuteNonQuery()
                End Using

                MessageBox.Show(
                    $"Borrow request for ""{bookTitle}"" submitted!" & Environment.NewLine &
                    "Please wait for admin approval.",
                    "Request Submitted", MessageBoxButtons.OK, MessageBoxIcon.Information)
                LoadBooks()

            Catch ex As MySqlException
                MessageBox.Show("Database error: " & ex.Message, "Error",
                                MessageBoxButtons.OK, MessageBoxIcon.Error)
            End Try
        End If

        ' ══════════════════════════════
        ' BORROWED VIEW — Return
        ' ══════════════════════════════
        If currentView = "borrowed" AndAlso colName = "colReturn" Then
            Dim borrowId As Integer = Convert.ToInt32(row.Cells("colBorrowId").Value)
            Dim bookId As Integer = Convert.ToInt32(row.Cells("colBookId").Value)
            Dim bookTitle As String = row.Cells("colTitle").Value?.ToString()

            Try
                Using conn As New MySqlConnection(connStr)
                    conn.Open()
                    Dim dupCmd As New MySqlCommand("
                        SELECT COUNT(*) FROM return_requests
                        WHERE borrow_record_id = @borrowId
                        AND status = 'pending'", conn)
                    dupCmd.Parameters.AddWithValue("@borrowId", borrowId)

                    If Convert.ToInt32(dupCmd.ExecuteScalar()) > 0 Then
                        MessageBox.Show("You already have a pending return request for this book.",
                                        "Duplicate Request", MessageBoxButtons.OK, MessageBoxIcon.Warning)
                        Return
                    End If
                End Using

                Dim confirm As DialogResult = MessageBox.Show(
                    $"Submit a return request for ""{bookTitle}""?",
                    "Confirm Return", MessageBoxButtons.YesNo, MessageBoxIcon.Question)
                If confirm <> DialogResult.Yes Then Return

                Using conn As New MySqlConnection(connStr)
                    conn.Open()
                    Dim cmd As New MySqlCommand("
                        INSERT INTO return_requests
                            (user_id, book_id, borrow_record_id, reason, status, requested_at)
                        VALUES
                            (@userId, @bookId, @borrowId, 'Returning borrowed book', 'pending', NOW())", conn)
                    cmd.Parameters.AddWithValue("@userId", loggedInUserId)
                    cmd.Parameters.AddWithValue("@bookId", bookId)
                    cmd.Parameters.AddWithValue("@borrowId", borrowId)
                    cmd.ExecuteNonQuery()
                End Using

                MessageBox.Show(
                    $"Return request for ""{bookTitle}"" submitted!" & Environment.NewLine &
                    "Please wait for admin approval.",
                    "Request Submitted", MessageBoxButtons.OK, MessageBoxIcon.Information)
                LoadBorrowedBooks()

            Catch ex As MySqlException
                MessageBox.Show("Database error: " & ex.Message, "Error",
                                MessageBoxButtons.OK, MessageBoxIcon.Error)
            End Try
        End If
    End Sub

    ' ══════════════════════════════════════════
    ' NAV BUTTONS
    ' ══════════════════════════════════════════
    Private Sub btnBooks_Click(sender As Object, e As EventArgs) Handles btnBooks.Click
        LoadBooks()
    End Sub

    Private Sub btnMyborrowedbooks_Click(sender As Object, e As EventArgs) Handles btnMyborrowedbooks.Click
        LoadBorrowedBooks()
    End Sub

    ' ══════════════════════════════════════════
    ' LOGOUT
    ' ══════════════════════════════════════════
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