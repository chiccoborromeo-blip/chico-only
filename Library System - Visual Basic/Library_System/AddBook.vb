Imports MySql.Data.MySqlClient

Public Class AddBook   ' ← Must match your form name exactly

    Private connStr As String = "server=localhost;userid=root;password=;database=library_db;"

    Private Sub cbCategory_Load(sender As Object, e As EventArgs) Handles MyBase.Load
        ' ── Category ComboBox ──
        cbCategory.Items.Clear()
        cbCategory.Items.AddRange(New String() {
        "Fiction",
        "Non-Fiction",
        "Science",
        "History",
        "Mystery",
        "Biography",
        "Technology",
        "Mathematics",
        "Literature",
        "Reference"
    })
        cbCategory.SelectedIndex = -1

        ' ── Available ComboBox ─
    End Sub
    Private Sub cbCategory_SelectedIndexChanged(sender As Object, e As EventArgs) Handles cbCategory.SelectedIndexChanged
        If cbCategory.SelectedIndex <> -1 Then
        End If
    End Sub

    Private Sub txtQuantity_TextChanged(sender As Object, e As EventArgs) Handles txtQuantity.TextChanged, txtGenre.TextChanged
        If txtQuantity.Text <> "" AndAlso Not Integer.TryParse(txtQuantity.Text, Nothing) Then
            MessageBox.Show("Quantity must be a number.", "Invalid Input", MessageBoxButtons.OK, MessageBoxIcon.Warning)
            txtQuantity.Clear()
        End If
    End Sub

    Private Function ValidateInputs() As Boolean
        If String.IsNullOrWhiteSpace(txtBookno.Text) Then
            MessageBox.Show("Book No is required.", "Validation", MessageBoxButtons.OK, MessageBoxIcon.Warning)
            txtBookno.Focus() : Return False
        End If
        If String.IsNullOrWhiteSpace(txtTitle.Text) Then
            MessageBox.Show("Title is required.", "Validation", MessageBoxButtons.OK, MessageBoxIcon.Warning)
            txtTitle.Focus() : Return False
        End If
        If String.IsNullOrWhiteSpace(txtAuthor.Text) Then
            MessageBox.Show("Author is required.", "Validation", MessageBoxButtons.OK, MessageBoxIcon.Warning)
            txtAuthor.Focus() : Return False
        End If
        If String.IsNullOrWhiteSpace(txtQuantity.Text) Then
            MessageBox.Show("Quantity is required.", "Validation", MessageBoxButtons.OK, MessageBoxIcon.Warning)
            txtQuantity.Focus() : Return False
        End If
        If Not Integer.TryParse(txtQuantity.Text, Nothing) Then
            MessageBox.Show("Quantity must be a valid number.", "Validation", MessageBoxButtons.OK, MessageBoxIcon.Warning)
            txtQuantity.Focus() : Return False
        End If
        Return True
    End Function

    Private Sub ClearForm()
        txtBookno.Clear()
        txtTitle.Clear()
        txtAuthor.Clear()
        txtGenre.Clear()
        txtQuantity.Clear()
        cbCategory.SelectedIndex = -1
    End Sub

    Private Sub btnAddbook_Click(sender As Object, e As EventArgs) Handles btnAddbook.Click
        If Not ValidateInputs() Then Return

        Try
            Using conn As New MySqlConnection(connStr)
                conn.Open()

                ' Check duplicate Book No
                Dim checkQuery As String = "SELECT COUNT(*) FROM books WHERE book_no = @book_no"
                Using checkCmd As New MySqlCommand(checkQuery, conn)
                    checkCmd.Parameters.AddWithValue("@book_no", txtBookno.Text.Trim())
                    If Convert.ToInt32(checkCmd.ExecuteScalar()) > 0 Then
                        MessageBox.Show("Book No already exists!", "Duplicate", MessageBoxButtons.OK, MessageBoxIcon.Warning)
                        txtBookno.Focus()
                        Return
                    End If
                End Using

                ' ── Auto-generate ISBN ──
                Dim autoIsbn As String = "ISBN-" & DateTime.Now.ToString("yyyyMMdd") & "-" & New Random().Next(1000, 9999).ToString()  ' Show it in the textbox too

                ' Insert book
                Dim query As String = "INSERT INTO books (book_no, genre, title, author, isbn, category, quantity, available) " &
                      "VALUES (@book_no, @genre, @title, @author, @isbn, @category, @quantity, @available)"
                '                                                                       

                Using cmd As New MySqlCommand(query, conn)
                    Dim qty As Integer = Convert.ToInt32(txtQuantity.Text.Trim())
                    cmd.Parameters.AddWithValue("@book_no", txtBookno.Text.Trim())
                    cmd.Parameters.AddWithValue("@genre", txtGenre.Text.Trim())
                    cmd.Parameters.AddWithValue("@title", txtTitle.Text.Trim())
                    cmd.Parameters.AddWithValue("@author", txtAuthor.Text.Trim())
                    cmd.Parameters.AddWithValue("@category", cbCategory.Text.Trim()) '
                    cmd.Parameters.AddWithValue("@isbn", autoIsbn)
                    cmd.Parameters.AddWithValue("@quantity", qty)
                    cmd.Parameters.AddWithValue("@available", qty)
                    cmd.ExecuteNonQuery()
                End Using
            End Using

            MessageBox.Show("Book added successfully!", "Success", MessageBoxButtons.OK, MessageBoxIcon.Information)
            ClearForm()

        Catch ex As MySqlException
            MessageBox.Show("Database error: " & ex.Message, "Error", MessageBoxButtons.OK, MessageBoxIcon.Error)
        Catch ex As Exception
            MessageBox.Show("Error: " & ex.Message, "Error", MessageBoxButtons.OK, MessageBoxIcon.Error)
        End Try
    End Sub

    Private Sub btnCancel_Click(sender As Object, e As EventArgs) Handles btnCancel.Click
        Dim confirm As DialogResult = MessageBox.Show(
            "Are you sure you want to cancel?",
            "Cancel", MessageBoxButtons.YesNo, MessageBoxIcon.Question)
        If confirm = DialogResult.Yes Then ClearForm()
        Me.Hide()
    End Sub

    ' Placeholder subs (for future use)
    Private Sub txtGenre_TextChanged(sender As Object, e As EventArgs)
    End Sub
    Private Sub txtTitle_TextChanged(sender As Object, e As EventArgs) Handles txtTitle.TextChanged
    End Sub
    Private Sub txtAuthor_TextChanged(sender As Object, e As EventArgs) Handles txtAuthor.TextChanged
    End Sub
    Private Sub txtIsbn_TextChanged(sender As Object, e As EventArgs)
    End Sub
    Private Sub txtCategory_TextChanged(sender As Object, e As EventArgs)
    End Sub

    Private Sub cbAvailable_SelectedIndexChanged(sender As Object, e As EventArgs)
        If cbCategory.SelectedIndex <> -1 Then
        End If
    End Sub
End Class